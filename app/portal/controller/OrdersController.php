<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use app\admin\model\AccountsModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Session;
use think\Request;
use app\admin\model\Upload;

/**
 * Class AdminIndexController
 * @package app\portal\controller
 * @adminMenuRoot(
 *     'name'   =>'门户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 30,
 *     'icon'   =>'th',
 *     'remark' =>'门户管理'
 * )
 */
class OrdersController extends AdminBaseController
{

    public function index(){

        $where['c.isdel'] = 0;
        $join = [
            ["carts c","c.order_id = o.order_id","left"],
            ["portal_post g","c.goods_id = g.id"],
            ["member m","m.id = o.member_id"],
        ];
        $field = 'o.*,c.id as cid,g.post_title,c.kc_year,c.check_time,c.is_owe,c.course_money,c.pay_money,c.course_time,c.check_name,m.username,m.phone,c
        .create_time,c.status,c.ck_type';
        $result = Db::name("orders")->alias("o")
            ->join($join)->field($field)
            ->where($where)
            ->order("o.id DESC")
            ->paginate(20);
        //dump($result);
        $this->assign('result', $result);
        $this->assign('page', $result->render());
        return $this->fetch();
    }

    public function add()
    {
        $request = Request::instance();
        $admin_id = cmf_get_current_admin_id();
        $groupid = Session::get('group_id');
        $adminname = Session::get('name');
        $check_name = Session::get('check_name');

        if($request->isAjax()){
            $page = $this->request->param("page");
            $goods_data = $this->getGoodslist(20,$page);
            $pages = $goods_data->render();
            $tempstr = '';
            foreach($goods_data as $vo){
                $tempstr = $tempstr.'<tr><td>'.$vo["id"].'</td><td>'.$vo["post_title"].'</td><td>'.date("Y-m-d H:i:s",$vo["create_time"]).'</td><td><input name="goods_id" class="goods_id" type="checkbox" value="'.$vo['id'].'" data-name="'.$vo['post_title'].'" /></td></tr>';
            }
            //dump($goods_data);
            $gogdslist['pages'] = $pages;
            $gogdslist['goods_data'] = $tempstr;
            return json($gogdslist);
        }

        //提交保存订单
        if ($this->request->isPost()) {
            //dump($_POST);
            $rs = 0;
            $array_id = [];
            $param = [];

            $ordercode = "A".date("Ymdhis").rand(1000,9999);

            //上传文件身份证正面
            $updata = new Upload();
            $file_path = '/upload/files/';
            $file_array = ["doc","docx","pdf","xlsx"];

            $member = Db::name('member')->where(array('phone' => $_POST['phone']))->find();
            //如果用户不存在，新增用户
            if (empty($member)) {
                $user['admin_id'] = $admin_id;
                $user['username'] = $_POST['username'];
                $user['phone'] = $_POST['phone'];
                $user['password'] = md5(md5($_POST['phone']));
                $user['cnname'] = $_POST['name'];
                $user['cardnum'] = $_POST['cardno'];
                $user['company'] = $_POST['company'];
                $user['province'] = $_POST['province'];
                $user['address'] = $_POST['address'];
                $user['email'] = $_POST['email'];

                if(request()->file('card_face')){
                    $uploads = $updata->uploadpic('card_face');
                    if ($uploads['res']) {
                        $user['card_face'] = $uploads['data'];
                    }
                }
                //上传文件身份证反面
                if(request()->file('card_back')) {
                    $uploads = $updata->uploadpic('card_back');
                    if ($uploads['res']) {
                        $user['card_back'] = $uploads['data'];
                    }
                }
                $userinfo = Db::name('member')->insert($user);
                $user['id'] = Db::name('member')->getLastInsID();
                if ($userinfo) {
                    $member = $user;
                } else {
                    $this->error('系统异常，请稍后提交！');
                }
            }
            else
            {
                $user['cnname'] = $_POST['name'];
                $user['cardnum'] = $_POST['cardno'];
                $user['company'] = $_POST['company'];
                $user['province'] = $_POST['province'];
                $user['address'] = $_POST['address'];
                $user['email'] = $_POST['email'];

                if(request()->file('card_face')){
                    $uploads = $updata->uploadpic('card_face');
                    if ($uploads['res']) {
                        $user['card_face'] = $uploads['data'];
                    }
                }
                //上传文件身份证反面
                if(request()->file('card_back')) {
                    $uploads = $updata->uploadpic('card_back');
                    if ($uploads['res']) {
                        $user['card_back'] = $uploads['data'];
                    }
                }
                Db::name('member')->where(array('phone' => $_POST['phone']))->update($user);
            }   //保存用户信息结束

            $param['check_name'] = $check_name;
            //创建时间
            $param['create_time'] = time();

            $goodsids = $_POST['goods_id'];
            if(strpos($goodsids,',')){
                $ids = explode(",",$goodsids);
                foreach($ids as $gid){
                    array_push($array_id,intval($gid));
                }
                $goodsids = implode(",",$array_id);
                $where = "id in (".$goodsids.")";
            }
            else
            { //array_push($array_id,intval($goodsids));
                $where = ["id"=>$goodsids];
            }
            //检验课程ID
            $goods = Db::name('portal_post')->where($where)->select();

            $kc_types = $_POST['kc_type'];      //课程年限
            $kc_prices = $_POST['kc_price'];    //课程价格
            $course_moneys = $_POST['course_money'];        //成交金额
            $pay_moneys = $_POST['pay_money'];      //已付金恩
            $i = 0;
            $rs = 0;
            foreach($goods as $vo){

                $where2['goods_id'] = $vo['id'];
                $where2['member_id'] = $member['id'];
                $isdata = Db::name("carts")->where($where2)->find();

                if(empty($isdata)){
                    $param['goods_id'] = $vo['id'];
                    $param['member_id'] = $member['id'];
                    $param['order_id'] = $ordercode;        //订单号，时间+随机数
                    $kcprice = $kc_prices[$i];
                    $kcmoney = $course_moneys[$i];
                    $paymoney = $pay_moneys[$i];

                    if(empty($kcmoney) || empty($kcprice)){
                        $type = 3;
                    }
                    elseif($kcmoney/$kcprice >= 0.8){
                        $type = 1;
                    }
                    else
                    {
                        $type = 2;
                    }

                    if($paymoney < $kcmoney){
                        $isowe = 1;
                    }
                    else
                    {
                        $isowe = 0;
                    }

                    $param['kc_year'] = $kc_types[$i];  //年限
                    $param['kc_price'] = $kc_prices[$i];    //价格
                    $param['course_money'] = $course_moneys[$i];    //成交费用
                    $param['pay_money'] = $pay_moneys[$i];      //已付费用
                    $param['is_owe'] = $isowe;      //是否欠费
                    if (!empty($_POST['owe_time']) && $isowe == 1) {
                        $param['owe_time'] = strtotime($_POST['owe_time']);
                    }
                    $param['status'] = 0;       //开课状态
                    $param['course_time'] = '';     //课程过期时间
                    $param['ck_type'] = $type;   //课程类型，正常1、非正常2、内部学习3
                    $param['isexam'] = 0;       //是否考过
                    $param['result'] = '';      //考试成绩单上传
                    $param['isprint'] = 0;      //是否打印过
                    //审核状态
                    $param['check'] = 1;        //是否审核
                    $param['isdel'] = 0;        //是否删除
                    $rs = Db::name('carts')->insert($param);
                    if($rs){
                        $i++;
                    }
                }
                //dump($param);
            }   //购物车保存结束

            if($i>0) {
                //保存订单到数据库
                //添加人员ID
                $datas['admin_id'] = $admin_id;
                //用户ID
                $datas['member_id'] = $member['id'];
                $datas['order_id'] = $ordercode;        //订单号，时间+随机数
                //分属角色
                $datas['group_id'] = $groupid;
                $datas['applicant'] = $adminname;
                $datas['teacher'] = $adminname;
                $datas['payment'] = $_POST['pay_way'];
                $datas['remarks'] = $_POST['remarks'];
                $pay_pics = array();
                //转款截图上传1
                for ($i = 1; $i < 5; $i++) {
                    if (request()->file('pay_pic' . $i)) {
                        $uploads = $updata->uploadpic('pay_pic' . $i);
                        if ($uploads['res']) {
                            //$datas['pay_pic'] = $uploads['data'];
                            array_push($pay_pics, $uploads['data']);
                        } else {
                            array_push($pay_pics, '');
                        }
                    }
                }
                $datas['pay_pic'] = serialize($pay_pics);

                //协议文件上传
                if (request()->file('agreement')) {
                    $uploads = $updata->uploadpic('agreement', $file_path, $file_array);
                    if ($uploads['res']) {
                        $datas['upload_file'] = $uploads['data'];
                    }
                }

                $agreements = array();
                //协议图片上传1
                for ($i = 1; $i < 5; $i++) {
                    if (request()->file('agreement' . $i)) {
                        $uploads = $updata->uploadpic('agreement' . $i);
                        if ($uploads['res']) {
                            //$datas['pay_pic'] = $uploads['data'];
                            array_push($agreements, $uploads['data']);
                        } else {
                            array_push($agreements, '');
                        }
                    }
                }
                $datas['agreement'] = serialize($agreements);
                $datas['update_time'] = time();
                //dump($datas);
                //exit();
                $rs = Db::name('orders')->insert($datas);
                if ($rs) {
                    $this->success('新增成功！', url('Orders/index'));
                } else {
                    $this->error('系统异常，请稍后提交！', url('Orders/index'));
                }
            }
            else
            {
                $this->error('课程已经存在，不能重复购买课程！', url('Orders/index'));
            }
        } //提交保存订单结束

        $goods_data = $this->getGoodslist();
        $pages = $goods_data->render();
        //dump($goods_data);
        //$gogdslist['page'] = $page;
        //$gogdslist['goods_data'] = $goods_data;
        //return $gogdslist;

        $applicant = Session::get("name");
        $this->assign('pages', $pages);
        $this->assign('goods_data', $goods_data);
        $this->assign('applicant', $applicant);
        $this->assign('check_name', $check_name);
        $this->assign('group_id', $groupid);
        return $this->fetch();
    }

    public function getGoodslist($limit=20,$page=1,$title='',$goods_type=''){
        $where["p.post_status"] = 1;

        if(!empty($title)){
            $where['p.post_title'] = array('like','%'.$title.'%');
        }
        $join = [
            ["portal_category_post c","c.post_id=p.id"]
        ];
        $field = 'p.*,c.category_id';
        if(!empty($goods_type)){
            $where["c.category_id"] = $goods_type;
        }
        $param['page'] = $page;
        $param['path'] = 'javascript:AjaxPage([PAGE]);';
        $goods_data = Db::name("portal_post")
            //->alias("p")->join($join)->field($field)->where($where)->order('p.id desc')
            ->paginate($limit,false,$param);
        //$page = $goods_data->render();
        return $goods_data;
    }

    //获取用户是否存
    public function getUser(){
        $phone = $this->request->param('phone');
        if(empty($phone)){
            return jsonOut(0,'');
        }
        if(!isPhone($phone)){
            return jsonOut(0,'');
        }
        $data = Db::name("member")->where(array('phone'=>$phone))->find();
        if(empty($data)){
            return jsonOut(0,'');
        }else{
            unset($data['password']);
            return jsonOut(1,$data);
        }
    }

    //获取商品
    public function getGoodsArray(){
        $goods_id = $this->request->param('goods_id');
        if(empty($goods_id)){
            jsonOut(0,'未查到课程');
        }
        $data = Db::name("portal_post")->where(array("id"=>["in",$goods_id]))->order('id asc')->select();
        //$data = json_decode($data,true);
        //$sql = Db::name("portal_post")->getLastSql();
        if(empty($data)){
            return jsonOut(0,'未查到课程');
        }else{
            return jsonOut(1,$data);
        }
    }


    public function edit(){

        $request = Request::instance();

        if($request->isAjax()){
            $page = $this->request->param("page");
            $goods_data = $this->getGoodslist(20,$page);
            $pages = $goods_data->render();
            $tempstr = '';
            foreach($goods_data as $vo){
                $tempstr = $tempstr.'<tr><td>'.$vo["id"].'</td><td>'.$vo["post_title"].'</td><td>'.date("Y-m-d H:i:s",$vo["create_time"]).'</td><td><input name="goods_id" class="goods_id" type="checkbox" value="'.$vo['id'].'" data-name="'.$vo['post_title'].'" /></td></tr>';
            }
            //dump($goods_data);
            $gogdslist['pages'] = $pages;
            $gogdslist['goods_data'] = $tempstr;
            return json($gogdslist);
        }

        //提交保存订单
        if ($this->request->isPost()) {

            $id = $this->request->param('id');
            $oid = $this->request->param('oid');
            $order_id = $this->request->param('order_id');
            $owe_time = $this->request->param('owe_time');

            //上传文件身份证正面
            $updata = new Upload();
            $file_path = '/upload/files/';
            $file_array = ["doc","docx","pdf","xlsx"];

            $datas['payment'] = $_POST['pay_way'];
            $datas['remarks'] = $_POST['remarks'];
            $pay_pics = array();
            //转款截图上传1
            for($i=1;$i<5;$i++) {
                if (request()->file('pay_pic'.$i)) {
                    $uploads = $updata->uploadpic('pay_pic'.$i);
                    if ($uploads['res']) {
                        //$datas['pay_pic'] = $uploads['data'];
                        array_push($pay_pics, $uploads['data']);
                    }
                }
                else
                {
                    $upimg = $_POST['pay_pic_'.$i];
                    array_push($pay_pics, $upimg);
                }
            }
            $datas['pay_pic'] = serialize($pay_pics);
            //协议文件上传
            if(request()->file('agreement')) {
                $uploads = $updata->uploadpic('agreement', $file_path, $file_array);
                if ($uploads['res']) {
                    $datas['upload_file'] = $uploads['data'];
                }
            }

            $agreements = array();
            //协议图片上传1
            for($i=1;$i<5;$i++) {
                if (request()->file('agreement'.$i)) {
                    $uploads = $updata->uploadpic('agreement'.$i);
                    if ($uploads['res']) {
                        //$datas['pay_pic'] = $uploads['data'];
                        array_push($agreements, $uploads['data']);
                    }
                }
                else
                {
                    $upimg = $_POST['agreement_'.$i];
                    array_push($agreements, $upimg);
                }
            }
            $datas['agreement'] = serialize($agreements);
            $datas['update_time'] = time();
            //exit();
            $rs = Db::name('orders')->where(array("id"=>$oid))->update($datas);

            if ($rs) {
                $parem['owe_time'] = strtotime($owe_time);
                Db::name('carts')->where(array("id"=>$id))->update($parem);

                $adminid = Session::get("name");
                $active = "编辑课程订单";
                $this->cource_log($id,$adminid,$active);

                $this->success('编辑成功！',url('Orders/index'));
            } else {
                $this->error('系统异常，请稍后提交！',url('Orders/index'));
            }
        }
        else
        {
            $id = $this->request->param('cid');
            $oid = $this->request->param('oid');
            $order_id = $this->request->param('order_id');
            $goods_data = $this->getGoodslist();
            $pages = $goods_data->render();
            $this->assign('pages', $pages);
            $this->assign('goods_data', $goods_data);

            $where['c.id'] = $id;
            $join = [
              ["orders o","o.order_id = c.order_id"]
            ];
            $field = "c.*,o.id as oid,o.remarks,o.payment,o.pay_pic,o.agreement,o.upload_file";
            $data = Db::name("carts")->alias("c")->join($join)->field($field)->where($where)->find();
            //dump(unserialize($data['pay_pic']));

            $join2 = [
                ["portal_post p","p.id=c.goods_id"],
                ["orders o","o.order_id = c.order_id"]
            ];
            $where2['c.order_id'] = $order_id;
            $field2 = "c.*,p.post_title,p.year_price1,p.year_price2,p.year_price3";
            $cartlist = Db::name("carts")->alias("c")->join($join2)->field($field2)->where($where2)->select();
            //dump($cartlist);
            $ids = array();
            foreach($cartlist as $vo){
                array_push($ids,$vo['goods_id']);
            }
            $this->assign('goods_id', implode(",",$ids));
            $this->assign('id', $id);
            $this->assign('oid', $data['oid']);
            $this->assign('order_id', $data['order_id']);
            $this->assign('cartlist', $cartlist);
            $this->assign('data', $data);
            return $this->fetch();
        }

    }

    public function delete(){

        $cid = $this->request->param('cid');
        $oid = $this->request->param('oid');
        //$order_id = $this->request->param('order_id');
        $datas['update_time'] = time();
        $rs = Db::name('orders')->where(array("id"=>$oid))->update($datas);
        if ($rs) {
            $parem['isdel'] = 1;
            Db::name('carts')->where(array("id"=>$cid))->update($parem);

            $adminid = Session::get("name");
            $active = "删除课程订单";
            $this->cource_log($cid,$adminid,$active);

            $this->success('删除成功！',url('Orders/index'));
        } else {
            $this->error('系统异常，请稍后提交！',url('Orders/index'));
        }
    }

    //写入开课系统操作日志
    public function cource_log($id,$adminid,$active){
        if(!empty($id) && !empty($adminid)){
            $data["cid"] = $id;
            $data["status"] = 0;
            $data["adminid"] = $adminid;
            $data["remark"] = $active;
            $data["datetime"] = time();
            Db::name("course_log")->insert($data);
        }
    }

    //开课日志列表
    public function logs(){

        $id = $this->request->param("cid");
        $where['status'] = 0;
        if(!empty($id)){
            $where['cid'] = $id;
        }
        $loglist = Db::name('course_log')->where($where)->order('id DESC ')->paginate(20);
        $this->assign('page', $loglist->render());// 赋值分页输出
        $this->assign('loglist', $loglist);
        return $this->fetch();
    }

    public function previews(){

        $id = $this->request->param("cid");
        $where['c.id'] = $id;
        $join = [
            ["orders o","o.order_id = c.order_id"],
            ["member m","o.member_id = m.id"],
            ["portal_post p","p.id = c.goods_id"]
        ];
        $field = "c.*,o.id as oid,o.remarks,o.payment,o.pay_pic,o.applicant,o.teacher,p.post_title,m.username,m.cnname,m.phone,m.cardnum,m.email,m.province,m
        .address,m.company";
        $data = Db::name("carts")->alias("c")->join($join)->field($field)->where($where)->find();
        $goodsid = $data['goods_id'];
        $goodstypes = $this->getXiangmu($goodsid);
        $data["classname"] = $goodstypes['classname3'];
        $data["classname2"] = $goodstypes['classname2'];

        $this->assign("data",$data);
        return $this->fetch();
    }

    //根据goodsid查询项目
    public function getXiangmu($goodsid){
        $goodsdata =  Db::name('portal_category_post')->where(array('post_id'=>$goodsid))->find();
        $cateid = $goodsdata['category_id'];

        $goodscate1 = Db::name('portal_category')->where(array('id'=>$cateid))->find();
        $cateid1 = $goodscate1['parent_id'];
        $catename1 = $goodscate1['name'];

        $goodscate2 = Db::name('portal_category')->where(array('id'=>$cateid1))->find();
        $cateid2 = $goodscate2['parent_id'];
        $catename2 = $goodscate2['name'];

        $goodscate3 = Db::name('portal_category')->where(array('id'=>$cateid2))->find();
        $cateid3 = $goodscate3['parent_id'];
        $catename3 = $goodscate3['name'];

        $data = array(
            "classname1" => $catename1,
            "classname2" => $catename2,
            "classname3" => $catename3,
            "pinyin" => $goodscate3["pinyin"]
        );
        return $data;
    }

    //打印订单 20190104 redwe
    public function printOrder(){
        $cid     = $this->request->param('cid');
        if(empty($cid)){
            $this->error('参数错误');
        }
        $where['c.id'] = $cid;
        $join = [
            ["orders o","o.order_id = c.order_id"],
            ["member m","o.member_id = m.id"],
            ["portal_post p","p.id = c.goods_id"]
        ];
        $field = "c.*,o.id as oid,o.remarks,o.payment,o.pay_pic,o.applicant,o.teacher,p.post_title,m.username,m.cnname,m.phone,m.cardnum,m.email,m.province,m
        .address,m.company";
        $data = Db::name("carts")->alias("c")->join($join)->field($field)->where($where)->find();
        if(empty($data)){
            $this->error('暂无数据');
        }
        $orderid = $data["order_id"];
        $goodsid = $data["goods_id"];
        $classname = $data['post_title'];
        $datetime = date("Y-m-d h:i:s",time());
        $this->assign('data',$data);

        $oldnum =  Db::name('print_log')->where(array('orderid'=>$cid))->find();

        $goodstypes = $this->getXiangmu($goodsid);
        //dump($goodstypes);
        if(!empty($goodstypes['classname3'])){
            $data["classname3"] = $goodstypes['classname3'];
        }

        $data["classname2"] = $goodstypes['classname2'];
        $data["pinyin"] = $goodstypes['pinyin'];
        $pingyin = $goodstypes['pinyin'];
        if(empty($oldnum)){
            $pingyin = strtoupper($pingyin);
            //dump($pingyin);
            if(empty($pingyin)){
                $pingyin = "A";
            }
            else
            {
                $pingyin = $pingyin[0];
            }
            $date = date("Ymd",time());

            $newnum =  Db::name('print_log')->order("id desc")->find();
            if($newnum){
                $numid = $newnum["listid"];
            }
            else
            {
                $numid = 0;
            }
            $sourceNumber = $numid + 1;
            //$numcode = substr(strval($sourceNumber+1000000),1);
            $numcode = rand(100000,999999);
            $orderCode = $date.$pingyin.$numcode;

            $savedata = array(
                "orderid" =>$cid,
                "printcode" => $orderCode,
                "datetime" => $datetime,
                "listid" => $sourceNumber
            );

            $result = Db::name('print_log')->insert($savedata);
            if($result){
                Db::name('carts')->where(array('id'=>$cid))->update(array("isprint"=>1));
            }
        }
        else
        {
            $orderCode = $oldnum['printcode'];
            $where = array("orderid"=>$orderid);
            $savedata = array(
                "printcode" => $orderCode,
                "datetime" => $datetime
            );
            $result = Db::name('print_log')->where($where)->update($savedata);
            if($result){
                $res = Db::name('carts')->where(array('id'=>$cid))->find();
                if(empty($res['isprint'])){
                    Db::name('carts')->where(array('id'=>$cid))->update(array("isprint"=>1));
                }
                else
                {
                    Db::name('carts')->where(array('id'=>$cid))->setInc("isprint");
                }
            }
            //$this->error("该记录已经存在");
        }
        //dump($data);
        $this->assign('cate',$goodstypes);
        $this->assign('orderCode',$orderCode);
        return $this->fetch();
    }

    public function getAccount($pid,$type,$filed='*'){
        if(empty($pid)){
            $pid = cmf_get_current_admin_id();
        }
        $data = Db::name("admin")->field($filed)->where(array('pid'=>$pid))->select();
        if(empty($data)){
            return array();
        }
        if($type == 1){
            return $data;
        }elseif ($type == 2){
            $new_arr = array();
            if($filed != '*'){
                foreach ($data as $k=>$v){
                    $new_arr[] = $v[$filed];
                }
                return $new_arr;
            }else{
                return $data;
            }
        }
        return array();
    }

    //开课审核列表
    public function checklist(){

        $username = $this->request->param('username');
        $group_id = Session::get("group_id");
        $where = array();
        $adminid = cmf_get_current_admin_id();

        if ($username) {
            $where['m.username'] = array('like','%'.$username.'%');
        }
        $ids = $this->getAccount('',2,'id');
        //$admin_id = $_SESSION['admin_id'];
        array_push($ids,$adminid);
        //dump($ids);
        if($group_id == 5){
            $where['o.admin_id'] = array('IN',trim(implode(',',$ids),','));
        }
        $where['c.isdel'] = 0;
        $where['c.check'] = 1;
        if($group_id == 3){
            $where = array('c.check'=>2,'c.ck_type'=>2,'c.status'=>2);
        }
        $join = [
            ["carts c","c.order_id = o.order_id","left"],
            ["portal_post g","c.goods_id = g.id"],
            ["member m","m.id = o.member_id"],
        ];
        $field = 'o.*,c.id as cid,g.post_title,c.kc_year,c.check,c.check_time,c.is_owe,c.course_money,c.pay_money,c.course_time,c.check_name,m.username,
        m.phone,c.create_time,c.status,c.ck_type,m.card_face,m.card_back';
        $result = Db::name("orders")->alias("o")
            ->join($join)->field($field)
            ->where($where)
            ->order("o.id DESC")
            ->paginate(20);

        //dump(Db::name("orders")->getLastSql());
        $this->assign('username', $username);
        $this->assign('order', $result);
        $this->assign('page', $result->render());// 赋值分页输出
        return $this->fetch();
    }

    //审核
    public function check()
    {
        if ($this->request->isPost()) {
            $id = $this->request->param("cid");
            $strname = '';
            $strres = "未通过";
            $groupid =Session::get("group_id");
            $order = Db::name('carts')->where(array("id"=>$id))->find();
            $check = $this->request->param('check');
            $typeid = $this->request->param('type');
            $check_note = $this->request->param('check_note');
            $course_time = $this->request->param('course_time');
            $data['check_time'] = time();

            if($check==2){
                $strres = "通过";
                $course_time = strtotime($course_time);
                if($course_time<time()){
                    $this->error('提交失败！课程过期时间不正确！');
                    exit();
                }
                $data['course_time'] = $course_time;
                if($groupid == 5){
                    if($typeid == '2'){
                        $data['status']=2;
                    }
                    else
                    {
                        $data['status']=1;
                    }
                    $strname = "总监";
                }
                if($groupid == 3){
                    if($typeid == 2){
                        $data['status']=1;
                    }
                    $strname = "经理";
                }
                if($groupid == 1){
                    $data['status']=1;
                    $strname = "管理员";
                }
            }

             $data['check'] = $check;
             $data['check_note'] = $check_note;

            $check_name = Session::get('name');
            $data['check_name'] = $check_name;
            $admin_id = cmf_get_current_admin_id();

            if($check==2 && $groupid != 3){
                $acc_model = new AccountsModel();
                $result=$acc_model->add_accounts($admin_id,$order['course_money'],2,$id);
                if($result['code']==500){
                    $this->error($result['msg']);
                    exit;
                }
            }
            $rs = Db::name('carts')->where(array('id'=>$id))->update($data);
            if($rs){
                $this->cource_log($id,$check_name,$strname."审核订单".$strres);
                $this->success('提交成功！');
            }else{
                $this->error('提交失败！请联系管理员');
            }
        } else {
            $id = $this->request->param("cid");
            $groupid = Session::get("group_id");
            //$kcdata = Db::name('carts')->where(array('id'=>$id))->find();
            $where['c.id'] = $id;
            $join = [
                ["orders o","o.order_id = c.order_id"],
                ["member m","o.member_id = m.id"],
                ["portal_post p","p.id = c.goods_id"]
            ];
            $field = "c.*,o.id as oid,o.remarks,o.payment,o.pay_pic,o.applicant,o.teacher,p.post_title,m.username,m.cnname,m.phone,m.cardnum,m.email,m.province,m
        .address,m.company";
            $kcdata = Db::name("carts")->alias("c")->join($join)->field($field)->where($where)->find();

            $this->assign("groupid",$groupid);
            $this->assign("kcdata",$kcdata);
            $this->assign("type",$kcdata['ck_type']);
            $this->assign("id",$id);
            return $this->fetch();
        }
    }

    //查看订单详情
    public function look()
    {
        $id = $this->request->param('cid');

        $where['c.id'] = $id;
        $join = [
            ["orders o","o.order_id = c.order_id"],
            ["member m","o.member_id = m.id"],
            ["portal_post p","p.id = c.goods_id"]
        ];
        $field = "c.*,o.id as oid,o.remarks,o.payment,o.pay_pic,o.applicant,o.teacher,o.agreement,o.upload_file,p.post_title,m.username,m.cnname,m.phone,m.cardnum,m.email,m.province,m
        .address,m.company";
        $data = Db::name("carts")->alias("c")->join($join)->field($field)->where($where)->find();
//dump($data);
        $this->assign('id', $id);
        $this->assign('data', $data);
        return $this->fetch();
    }

}
