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

        $where['c.status']=1;
        $join = [
            ["carts c","c.order_id = o.order_id","left"],
            ["portal_post g","c.goods_id = g.id"],
            ["member m","m.id = o.member_id"],
        ];
        $field = 'o.*,c.id as cid,g.post_title,c.kc_year,c.check_time,c.is_owe,c.course_money,c.pay_money,c.course_time,c.check_name,m.username,m.phone,c
        .create_time';
        $result = Db::name("orders")->alias("o")
            ->join($join)->field($field)
            //->where($where)
            ->order("o.id DESC")
            ->paginate(20);
        $this->assign('result', $result);
        $this->assign('page', $result->render());
        return $this->fetch();
    }

    public function add()
    {
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
            //dump($_POST);
            $rs = 0;
            $array_id = [];
            $param = [];

            $ordercode = date("Ymdhis").rand(1000,9999);

            //上传文件身份证正面
            $updata = new Upload();
            $file_path = '/upload/files/';
            $file_array = ["doc","docx","pdf","xlsx"];

            $member = Db::name('member')->where(array('phone' => $_POST['phone']))->find();
            //如果用户不存在，新增用户
            if (empty($member)) {
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

            //保存课程信息到购物车表
            $admin_id = cmf_get_current_admin_id();
            $groupid = Session::get('group_id');
            $adminname = Session::get('name');

            $groups = Db::name("admin")->where(array("id"=>$admin_id))->find();
            $pid = $groups["pid"];

            $badmin = Db::name("admin")->where(array("id"=>$pid))->find();
            //dump($badmin);

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
                    $param['check'] = 1;        //是否欠费

                    if($groupid == 6){
                        $param['check_name'] = $badmin['username'];
                    }
                    else
                    {
                        $param['check_name'] = $adminname;
                    }
                    //创建时间
                    $param['create_time'] = time();
                    $rs = Db::name('carts')->insert($param);
                    $i++;
                }
                //dump($param);
            }   //购物车保存结束

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
            for($i=1;$i<5;$i++)
            {
                if(request()->file('pay_pic'.$i)) {
                    $uploads = $updata->uploadpic('pay_pic'.$i);
                    if ($uploads['res']) {
                        //$datas['pay_pic'] = $uploads['data'];
                        array_push($pay_pics,$uploads['data']);
                    }
                    else
                    {
                        array_push($pay_pics,'');
                    }
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
                      } else {
                          array_push($agreements, '');
                      }
                  }
            }
            $datas['agreement'] = serialize($agreements);

            //dump($datas);
            //exit();
            $rs = Db::name('orders')->insert($datas);
            if ($rs) {
                $this->success('新增成功！',url('Orders/index'));
            } else {
                $this->error('系统异常，请稍后提交！',url('Orders/index'));
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

            //dump($datas);
            //exit();
            $rs = Db::name('orders')->where(array("id"=>$oid))->update($datas);
            if ($rs) {
                $this->success('编辑成功！',url('Orders/index'));
            } else {
                $this->error('系统异常，请稍后提交！',url('Orders/index'));
            }

        }
        else
        {
            $id = $this->request->param('cid');
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
            $this->assign('id', $id);
            $this->assign('oid', $data['oid']);
            $this->assign('data', $data);
            return $this->fetch();
        }

    }

}
