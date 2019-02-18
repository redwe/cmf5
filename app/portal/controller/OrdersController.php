<?php
namespace app\portal\controller;

use app\admin\model\AccountsModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Session;
use think\Request;
use app\admin\model\Upload;
use app\portal\model\OrdersModel;
use phpmailerException;

class OrdersController extends AdminBaseController
{
    //转移订单数据
    public function moveDatas(){
        $loopData = Db::name("course_order")->select();
/*
        foreach($loopData as $key=>$vo){

                        $order_id = getOrderCode();

                        $pay_pics = [
                            $vo["pay_pic"],
                            $vo["pay_pic1"],
                            $vo["pay_pic2"],
                            $vo["pay_pic3"],
                            $vo["pay_pic4"],
                        ];

                        $member_id = $vo['member_id'];
                        $admin_id = $vo['admin_id'];

                        $cards = [];
                        if(!empty($vo["card_positive"])){
                            $cards["card_face"] = $vo["card_positive"];
                        }
                        if(!empty($vo["card_positive1"])) {
                            $cards["card_back"] = $vo["card_positive1"];
                        }
                        if(!empty($vo["cardno"])) {
                            $cards["cardnum"] = $vo["cardno"];
                        }
                        if(!empty($vo["student_email"])) {
                            $cards["email"] = $vo["student_email"];
                        }
                        if(!empty($vo["name"])) {
                            $cards["cnname"] = $vo["name"];
                        }
                        if(!empty($vo["name"])) {
                            $cards["admin_id"] = $admin_id;
                        }

                        if($cards){
                            //Db::name("member")->where(array("id"=>$member_id))->update($cards);
                        }

                        $agreement = [
                            $vo["agreement1"],
                            $vo["agreement2"],
                        ];

                        $pay_pics = serialize($pay_pics);
                        $agreement = serialize($agreement);

                        $order_data = [
                            "order_id" => $order_id,
                            "admin_id" => $admin_id,
                            "member_id" => $member_id,
                            "payment" => $vo['pay_way'],
                            "pay_pic" => $pay_pics,
                            "agreement" => $agreement,
                            "upload_file" => $vo['agreement'],
                            "applicant" => $vo['applicant'],
                            "group_id" => $vo['group_id'],
                            "teacher" => $vo['zhaosheng_teacher'],
                            "update_time" => $vo['time'],
                            "remarks" => $vo['remarks']
                        ];

                        $res = Db::name("orders")->insert($order_data);
                        $oid = Db::name("orders")->getLastInsID();

                        $cart_data = [
                            "id" => $vo['id'],
                            "order_id"=>$order_id,
                            "goods_id"=>$vo['goods_id'],
                            "member_id"=>$member_id,
                            "kc_year"=>$vo['kc_type'],
                            "kc_price"=>$vo['kc_price'],
                            "course_money"=>$vo['course_money'],
                            "pay_money"=>$vo['pay_money'],
                            "ck_type"=>$vo['type'],
                            "is_owe"=>$vo['type'],
                            "owe_time"=>$vo['owe_time'],
                            "status"=>$vo['status'],
                            "course_time"=>$vo['course_time'],
                            "check" => $vo["check"],
                            "check_name" => $vo["check_name"],
                            "check_note" => $vo['check_note'],
                            "check_time" => $vo["check_time"],
                            "isexam" => $vo['isexam'],
                            "result" => $vo['result'],
                            "isprint" => $vo['isprint'],
                            "xiu_status" => $vo["xiu_status"],
                            "tk_status" => $vo["tk_status"],
                            "isdel" => 0,
                            "create_time" => $vo["time"]
                        ];

                        $carts = Db::name("carts")->insert($cart_data);
                        $cartid = Db::name("carts")->getLastInsID();

                        if(!empty($vo['tk_note'])){
                            $tuikes = [
                                "order_id" => $order_id,
                                "cid" => $cartid,
                                "tk_note" => $vo['tk_note'],
                                "tk_status" => $vo['tk_status'],
                                "tkstatus_note" => $vo['tkstatus_note'],
                                "datetime" => time(),
                                "checktime" => time(),
                            ];
                           $result = Db::name("tuikes")->insert($tuikes);
                        }
        }
*/
        dump("OK!");
    }

    public function index(){

        $group_id = Session::get("group_id");
        $teacher = $this->request->param("teacher");
        $applicant = $this->request->param("applicant");
        $checkname = $this->request->param("checkname");
        $type_id = $this->request->param('type_id');
        $username = $this->request->param('username');
        $cnname = $this->request->param('cnname');
        $goods_name = $this->request->param("goods_name");
        $phone = $this->request->param('phone');
        $province = $this->request->param("province");
        //$badmin = $this->request->param("badmin");

        $start_time = $this->request->param('start_time');
        $end_time = $this->request->param('end_time');

        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);

        $type = $this->request->param("type");
        $this->assign('type',$type);

        if (!empty($type)) {
            $where['c.ck_type'] = $type;
        }

        $isqf = $this->request->param("isqf");
        $this->assign('isqf',$isqf);

        if (!empty($isqf)) {
            if($isqf==1){
                $where['c.is_owe'] = 0;
            }
            else
            {
                $where['c.is_owe'] = 1;
            }
        }

        $check = $this->request->param("check");
        $this->assign('check',$check);

        if (!empty($check)) {
            if($check==2){
                $where['c.check'] = 2;
            }
            else
            {
                $where['c.check'] = 3;
            }
        }

        $where['c.isdel'] = 0;

        if ($applicant) {
            $where['o.applicant'] = array('like','%'.$applicant.'%');
        }
        if ($checkname) {
            $where['c.check_name'] = array('like','%'.$checkname.'%');
        }
        if ($teacher) {
            $where['o.teacher'] = array('like','%'.$teacher.'%');
        }
        if(!empty($username)){
            $where['m.username'] = $username;
        }
        if(!empty($cnname)){
            $where['m.cnname'] = $cnname;
        }
        if(!empty($goods_name)){
            $where['p.post_title'] = array('like','%'.$goods_name.'%');
        }
        if(!empty($phone)){
            $where['m.phone'] = $phone;
        }
        if(!empty($province)){
            $where['m.province'] = array('like','%'.$province.'%');
        }
        if(!empty($badmin)){
            $where['c.check_name'] = array('like','%'.$badmin.'%');
        }

        if(!empty($start_time)){
            $where['c.check_time'] = array('gt',strtotime($start_time));
        }
        if(!empty($end_time)){
            $where['c.check_time'] = array('lt',strtotime($end_time));
        }
        if(!empty($start_time) && !empty($end_time)){
            $where['c.check_time'] = array('between',array(strtotime($start_time),strtotime($end_time)));
        }

        $orderObject = new OrdersModel();
        $cateids = $orderObject->getCateids($type_id);

        if(!empty($type_id)){
            if(!empty($cateids)){
                $gids = $orderObject->getGoodsid($cateids);
                //dump($cateids);
                $gids2 = implode(",",$gids);
                $where['c.goods_id'] = array('in', $gids2);
            }
        }
        $types = $orderObject->getTopCate();
        $result = $orderObject->getOrderlist($where,20);
        //dump($orderObject->getLastSql());

        $this->assign('types',$types);
        $this->assign('type_id',$type_id);
        $this->assign('group_id',$group_id);//用户组ID
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

        //$info= Db::name('portal_category')->field('id,parent_id as parentid,path as parentstr,name as classname,year,description')->select();
        $info= Db::name('kecheng_cate')->field('id,parentid,parentstr,classname,level')->select();
        $info = json_decode($info,true);
        $info = unlimitedForLevel($info);
        //dump($info);
        $this->assign('info',$info);

        if($request->isAjax()){
            $keyword = $this->request->param("title");
            $page = $this->request->param("page");
            $goods_type = $this->request->param("kcid");

            $orderObject = new OrdersModel();
            $goods_data = $orderObject->getGoodslist(20,$page,$keyword,$goods_type);
            $pages = $goods_data->render();
            $tempstr = '';
            foreach($goods_data as $vo){
                $tempstr = $tempstr.'<tr><td>'.$vo["id"].'</td><td><span>'.$vo["post_title"].'</span></td><td>'.date("Y-m-d H:i:s",$vo["create_time"]).'</td><td><input name="goods_id" class="goods_id" type="checkbox" value="'.$vo['id'].'" data-name="'.$vo['post_title'].'" /></td></tr>';
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
            $ordercode = getOrderCode();
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
                $user['gouke'] = 1;

                if(request()->file('card_face')){
                    $uploads = $updata->uploadpic('card_face');
                    if ($uploads['res']) {
                        $user['card_face'] = $uploads['fileurl'];
                    }
                }
                //上传文件身份证反面
                if(request()->file('card_back')) {
                    $uploads = $updata->uploadpic('card_back');
                    if ($uploads['res']) {
                        $user['card_back'] = $uploads['fileurl'];
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
                $user['gouke'] = 1;

                if(request()->file('card_face')){
                    $uploads = $updata->uploadpic('card_face');
                    if ($uploads['res']) {
                        $user['card_face'] = $uploads['fileurl'];
                    }
                }
                //上传文件身份证反面
                if(request()->file('card_back')) {
                    $uploads = $updata->uploadpic('card_back');
                    if ($uploads['res']) {
                        $user['card_back'] = $uploads['fileurl'];
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
                            //$datas['pay_pic'] = $uploads['fileurl'];
                            array_push($pay_pics, $uploads['fileurl']);
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
                        $datas['upload_file'] = $uploads['fileurl'];
                    }
                }

                $agreements = array();
                //协议图片上传1
                for ($i = 1; $i < 5; $i++) {
                    if (request()->file('agreement' . $i)) {
                        $uploads = $updata->uploadpic('agreement' . $i);
                        if ($uploads['res']) {
                            //$datas['pay_pic'] = $uploads['fileurl'];
                            array_push($agreements, $uploads['fileurl']);
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
        $orderObject = new OrdersModel();
        $goods_data = $orderObject->getGoodslist();
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

        $info= Db::name('kecheng_cate')->field('id,parentid,parentstr,classname,level')->select();
        $info = json_decode($info,true);
        $info = unlimitedForLevel($info);
        //dump($info);
        $this->assign('info',$info);

        if($request->isAjax()){
            $page = $this->request->param("page");
            $keyword = $this->request->param("title");
            $goods_type = $this->request->param("kcid");
            $orderObject = new OrdersModel();
            $goods_data = $orderObject->getGoodslist(20,$page,$keyword,$goods_type);
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
                        //$datas['pay_pic'] = $uploads['fileurl'];
                        array_push($pay_pics, $uploads['fileurl']);
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
                    $datas['upload_file'] = $uploads['fileurl'];
                }
            }

            $agreements = array();
            //协议图片上传1
            for($i=1;$i<5;$i++) {
                if (request()->file('agreement'.$i)) {
                    $uploads = $updata->uploadpic('agreement'.$i);
                    if ($uploads['res']) {
                        //$datas['pay_pic'] = $uploads['fileurl'];
                        array_push($agreements, $uploads['fileurl']);
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

            $rs = Db::name('orders')->where(array("order_id"=>$order_id))->update($datas);

            if ($rs) {
                $parem['owe_time'] = strtotime($owe_time);
                $parem['check'] = 1;
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

            $orderObject = new OrdersModel();
            $goods_data = $orderObject->getGoodslist();
            $pages = $goods_data->render();
            $this->assign('pages', $pages);
            $this->assign('goods_data', $goods_data);

            $where['c.id'] = $id;
            $where['c.isdel'] = 0;
            $orderObject = new OrdersModel();
            $data = $orderObject->getOrderlist($where,0);

            $join2 = [
                ["portal_post p","p.id=c.goods_id"],
                ["orders o","o.order_id = c.order_id"]
            ];
            $where2['c.order_id'] = $order_id;
            $where2['c.isdel'] = 0;
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

        $orderObject = new OrdersModel();
        $data = $orderObject->getOrderlist($where,0);

        $goodsid = $data['goods_id'];
        $orderObject = new OrdersModel();
        $goodstypes = $orderObject->getXiangmu($goodsid);
        $data["classname"] = $goodstypes['classname3'];
        $data["classname2"] = $goodstypes['classname2'];

        $this->assign("data",$data);
        return $this->fetch();
    }

    //打印订单 20190104 redwe
    public function printOrder(){
        $cid     = $this->request->param('cid');
        if(empty($cid)){
            $this->error('参数错误');
        }
        $where['c.id'] = $cid;

        $orderObject = new OrdersModel();
        $data = $orderObject->getOrderlist($where,0);

        if(empty($data)){
            $this->error('暂无数据');
        }
        $orderid = $data["order_id"];
        $goodsid = $data["goods_id"];
        $classname = $data['post_title'];
        $datetime = date("Y-m-d h:i:s",time());
        $this->assign('data',$data);

        $oldnum =  Db::name('print_log')->where(array('orderid'=>$cid))->find();

        $orderObject = new OrdersModel();
        $goodstypes = $orderObject->getXiangmu($goodsid);
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

    //开课审核列表
    public function checklist(){

        $username = $this->request->param('username');
        $group_id = Session::get("group_id");
        $where = array();
        $adminid = cmf_get_current_admin_id();

        if ($username) {
            $where['m.username'] = array('like','%'.$username.'%');
        }
        $orderObject = new OrdersModel();
        $ids = $orderObject->getAccount('',2,'id');
        //$admin_id = $_SESSION['admin_id'];
        array_push($ids,$adminid);
        //dump($ids);
        if($group_id == 5){
            $where['o.admin_id'] = array('IN',trim(implode(',',$ids),','));
        }
        $where['c.check'] = 1;
        if($group_id == 3){
            $where = array('c.check'=>2,'c.ck_type'=>2,'c.status'=>2);
        }
        $orderObject = new OrdersModel();
        $result = $orderObject->getOrderlist($where,20);

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
            $orderObject = new OrdersModel();
            $kcdata = $orderObject->getOrderlist($where,0);

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
        $orderObject = new OrdersModel();
        $data = $orderObject->getOrderlist($where,0);
//dump($data);
        $this->assign('id', $id);
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function remark(){

        if ($this->request->isPost()) {

            $id = $this->request->param("cid");
            $content = $this->request->param('content');
            $admin_name = Session::get('name');

            $data['order_id'] = $id;
            $data['content'] = $content;
            $data['admin_name'] = $admin_name;
            $data['status'] = 1;
            $data['create_time'] = time();

            //上传图片
            $updata = new Upload();
            if(request()->file('thumb')){
                $uploads = $updata->uploadpic('thumb');
                if ($uploads['res']) {
                    $data['thumb'] = $uploads['fileurl'];
                }
            }
            $file_path = '/upload/files/';
            $file_array = ["doc","docx","pdf","xlsx"];
            //上传文件
            if(request()->file('agreement')){
                $uploads = $updata->uploadpic('agreement',$file_path,$file_array);
                if ($uploads['res']) {
                    $data['agreement'] = $uploads['fileurl'];
                }
            }

            $rs = Db::name('remarks')->where(array('id'=>$id))->insert($data);
            if($rs){
                $this->success('备注成功！',url('Orders/guestList'));
            }else{
                $this->error('提交失败！请联系管理员',url('Orders/guestList'));
            }
        } else {
            $id = $this->request->param("cid");
            $groupid =Session::get("group_id");

            $where['c.id'] = $id;
            $orderObject = new OrdersModel();
            $kcdata = $orderObject->getOrderlist($where,0);

            $remarklist = Db::name('remarks')->where(array('order_id'=>$id))->select();
            $this->assign("remarklist",$remarklist);
            //dump($remarklist);
            $this->assign("groupid",$groupid);
            $this->assign("kcdata",$kcdata);
            $this->assign("id",$id);
            return $this->fetch();
        }
    }

    public function guestlist(){

        $group_id = Session::get("group_id");
        $teacher = $this->request->param("teacher");
        $type_id = $this->request->param('type_id');
        $name = $this->request->param('cnname');
        $goods_name = $this->request->param("goods_name");
        $phone = $this->request->param('phone');
        $province = $this->request->param("province");
        $badmin = $this->request->param("badmin");

        $where = array();

        if ($teacher) {
            $where['o.teacher'] = array('like','%'.$teacher.'%');
        }
        if(!empty($name)){
            $where['m.cnname'] = array('like','%'.$name.'%');
        }
        if(!empty($goods_name)){
            $where['p.post_title'] = array('like','%'.$goods_name.'%');
        }
        if(!empty($phone)){
            $where['m.phone'] = $phone;
        }
        if(!empty($province)){
            $where['m.province'] = array('like','%'.$province.'%');
        }
        if(!empty($badmin)){
            $where['c.check_name'] = array('like','%'.$badmin.'%');
        }

        $orderObject = new OrdersModel();
        if(!empty($type_id)) {
            $cateids = $orderObject->getCateids($type_id);
            if (!empty($cateids)) {
                $gids = $orderObject->getGoodsid($cateids);
                //dump($cateids);
                $gids2 = implode(",", $gids);
                $where['c.goods_id'] = array('in', $gids2);
            }
        }
        $order = $orderObject->getOrderlist($where,20);
        $types = $orderObject->getTopCate();

        $this->assign('types',$types);
        $this->assign('type_id',$type_id);
        $this->assign('order', $order);
        $this->assign('page', $order->render());// 赋值分页输出
        $this->assign('group_id',$group_id);//用户组ID
        return $this->fetch();
    }


    public function sendMessage(){

        $allids = $this->request->param("allids");
        $where["c.isdel"] = 0;
        $phones = [];
        if(!empty($allids)){
            $where = array('m.id'=>["in",$allids]);
            $orderObject = new OrdersModel();
            $userlist = $orderObject->getOrderlist($where,20);

            foreach($userlist as $vo){
                array_push($phones,$vo['phone']);
            }
            if(count($phones)==1){
                $phone = $phones[0];
            }
            else
            {
                $phone = implode($phones,",");
            }
        }
        else
        {
            $this->error('请选择用户！',url("Orders/guestList"));
            exit();
        }

        $this->assign('phone', $phone);
        $this->assign('ids',$allids);
        return $this->fetch();
    }

    //批量发送短信
    public function sendGSM(){
        if ($this->request->isPost()) {
            $phonelist = [];
            $phones = $this->request->param("phone");
            $content = $this->request->param("content");

            if(empty($phones) || empty($content)){
                $this->error('手机号和短信内容不能为空！',url("Orders/guestList"));
                exit();
            }
            if(strpos($phones,",")){
                $phonelist = explode(",",$phones);
            }
            else
            {
                array_push($phonelist,$phones);
            }
            if(sendMsg2($phones,$content)){
                $this->success("发送成功！",url("Orders/guestList"));
            } else {
                $this->error('系统异常，请稍后提交！',url("Orders/guestList"));
            }
            //dump($phones);
        }
    }

    public function sendEmail(){
        $allids = $this->request->param("allids");
        $where["c.isdel"] = 0;
        $Email = [];
        if(!empty($allids)){
            $where = array('c.id'=>["in",$allids]);
            $orderObject = new OrdersModel();
            $userlist = $orderObject->getOrderlist($where,20);
            foreach($userlist as $vo){
                if(!empty($vo['email'])){
                    array_push($Email,$vo['email']);
                }
            }
            if(count($Email)==1){
                $Email = $Email[0];
            }
            else
            {
                $Email = implode($Email,",");
            }
        }
        else
        {
            $this->error('请选择用户！',url("Orders/guestList"));
            exit();
        }
        //dump($Email);
        $this->assign('Email', $Email);
        $this->assign('ids',$allids);
        return $this->fetch();
    }


    public function sendMail(){

        $email = $this->request->param("email");
        $subject = $this->request->param("subject");
        $content = $this->request->param("content");
        $nickname = $this->request->param("nickname");
        $from = '3007081055@qq.com';     //发送邮箱地址
        $password = "efevnfwxdrouddfj"; //发送邮箱密码
        $Attachment = ''; //附件文件地址
        //上传图片
        $updata = new Upload();

        $file_path = '/upload/files/';
        $file_array = ["jpg","png","rar","zip","txt","doc","docx","pdf","xlsx"];
        //上传文件
        if(request()->file('agreement')){
            $uploads = $updata->uploadpic('agreement',$file_path,$file_array);
            if ($uploads['res']) {
                $Attachment = $uploads['fileurl'];
            }
        }

        if(empty($email) || empty($content)){
            $this->error('邮箱地址和内容不能为空！',url("Orders/guestList"));
            exit();
        }
        $orderObject = new OrdersModel();
        $result = $orderObject->send_email($nickname, $from, $password, $email, $subject, $content,$Attachment);
        //dump($result);
        if($result){
            $this->success("发送成功！",url("Orders/guestList"));
        } else {
            $this->error('系统异常，请稍后提交！',url("Orders/guestList"));
        }
    }

    //导入
    public function daoru(){
        if ($this->request->isPost()) {
            // 上传
            $updata = new Upload();
            $file_name = '';
            $cart = [];
            $order = [];
            $user = [];
            $rootUrl = $_SERVER['DOCUMENT_ROOT'];

            $file_path = '/upload/excel/';
            $file_array = ['xls','xlsx'];
            //上传文件
            if(request()->file('agreement')){
                $uploads = $updata->uploadpic('agreement',$file_path,$file_array);
                if ($uploads['res']) {
                    $file_name = $uploads['fileurl'];
                }
            }
            $orderObject = new OrdersModel();
            $exl = $orderObject->import_exl($rootUrl.$file_name);

            //获取所有的课程信息
            $goodslist = Db::name("portal_post")->select();
            $goods_data = arrayValueToKey($goodslist,'id');
            array_splice($exl,0,1); //删除第一个表头数组元素，不能用unset(),否则索引0将不存在。
            $count = count($exl);
            $orderObject = new OrdersModel();
            for($i=0; $i<$count; $i++){
                //$ordercode = getOrderCode();
                    $kc_price = 0;
                    switch ($exl[$i]['kc_year']){
                        case 1:
                            $kc_price = $goods_data[$exl[$i]['goods_id']]['kc1_price'];
                            break;
                        case 2:
                            $kc_price = $goods_data[$exl[$i]['goods_id']]['kc2_price'];
                            break;
                        case 3:
                            $kc_price = $goods_data[$exl[$i]['goods_id']]['kc3_price'];
                            break;
                        default:$kc_price = 0;
                    }
                    if(empty($kc_price)){
                        $kc_price = 0;
                    }
                    $phone = $exl[$i]['phone'];
                    $where['phone'] = $phone;
                    $userdatas = Db::name("member")->where($where)->find();
                    //dump($exl[$i]['phone']);
                    if(empty($userdatas)){
                        //continue;   //如果用户不存在，跳过此记录。
                          //或插入新用户
                        $user[$i]['password'] = cmf_password($exl[$i]['phone']);
                        $user[$i]['regtime'] = time();
                        $user[$i]['status'] = 1;
                        $user[$i]['regip']   = get_client_ip(0, true);
                        Db::name("members")->insert($user[$i]);
                        $userid = Db::name("members")->getLastInsID();
                    }
                    else
                    {
                        $userid = $userdatas["id"];
                    }
                    $adminidData = $orderObject->getAdminid($exl[$i]['applicant']);

                    $course_money = $exl[$i]['course_money'];
                    $pay_money = $exl[$i]['pay_money'];

                    $cart[$i]['goods_id']=$exl[$i]['goods_id'];
                    //$cart[$i]['order_id'] = $ordercode;
                    $cart[$i]['member_id'] = $userid;
                    $cart[$i]['course_time']=$exl[$i]['course_time'];
                    $cart[$i]['course_money']=$course_money;
                    $cart[$i]['pay_money']=$pay_money;

                    $cart[$i]['is_owe']=$course_money > $pay_money ? 1 : 0;
                    $cart[$i]['ck_type']=1;
                    $cart[$i]['create_time']=$exl[$i]['time'];
                    $cart[$i]['check']=2;
                    $cart[$i]['check_name'] = $adminidData['shenher'];
                    $cart[$i]['status']=1;
                    $cart[$i]['isdel']=0;
                    $cart[$i]['kc_year']=$exl[$i]['kc_year'];
                    $cart[$i]['kc_price'] = $kc_price;
                    //$cart[$i]['post_title'] = $goods_data[$exl[$i]['goods_id']]['title'];

                    $order[$i]['admin_id'] = $adminidData['id'];
                    $order[$i]['member_id'] = $userid;
                    $order[$i]['payment']=$exl[$i]['payment'];
                    //$order[$i]['applicant'] = 'admin';
                    $order[$i]['teacher']=$exl[$i]['teacher'];
                    $order[$i]['applicant'] = $exl[$i]['applicant'];
                    $order[$i]['group_id'] = $adminidData['group_id'];

                    //存储用户表
                    $user[$i]['username']=$exl[$i]['username'];
                    $user[$i]['phone']=$exl[$i]['phone'];
                    $user[$i]['email']=$exl[$i]['email'];
            }
            //导入之前 检查是否重复开课
            $count2 = count($cart);
            for($i=0; $i<$count2; $i++){
                if(isset($cart[$i]['member_id'])){
                    $member_id = $cart[$i]['member_id'];
                }
                else
                {
                    $member_id = 0;
                }
                if(isset($cart[$i]['goods_id'])){
                    $goods_id = $cart[$i]['goods_id'];
                }
                else
                {
                    $goods_id = 0;
                }

                $where_Order = array(
                    'member_id'=>$member_id,
                    'teacher'=>$order[$i]['teacher']
                );
                //检测是否存在该用户订单，存在则合并订单，不存在则新建订单
                $orderData = Db::name("orders")->where($where_Order)->find();
                if(empty($orderData)){
                    $ordreCode = getOrderCode();
                    $order[$i]['order_id'] = $ordreCode;
                    Db::name("orders")->insert($order[$i]);
                }
                else
                {
                    $ordreCode = $orderData['order_id'];
                }
                $cart[$i]["order_id"] = $ordreCode;
                $where = array(
                    'member_id'=>$member_id,
                    'goods_id'=>$goods_id,
                    'ck_type'=>1
                );
                Db::name("carts")->where($where)->delete();     //删除重复的课程
                Db::name("carts")->insert($cart[$i]);
            }
            if($count2>0){
                $this->success('成功导入'.$count2.'条数据!',url('Orders/index'));
            }else{
               $this->error('导入失败');
            }
            exit();
        }
        return $this->fetch();
    }

    //导出
    public function daochu(){

        if ($this->request->isPost()) {
            $group_id = Session::get("group_id");
            $teacher = $this->request->param("teacher");
            $type_id = $this->request->param('type_id');
            $name = $this->request->param('cnname');
            $goods_name = $this->request->param("goods_name");
            $phone = $this->request->param('phone');
            $province = $this->request->param("province");
            $badmin = $this->request->param("badmin");

            $where["c.isdel"] = 0;

            if ($teacher) {
                $where['o.teacher'] = array('like','%'.$teacher.'%');
            }
            if(!empty($name)){
                $where['m.cnname'] = array('like','%'.$name.'%');
            }
            if(!empty($goods_name)){
                $where['p.post_title'] = array('like','%'.$goods_name.'%');
            }
            if(!empty($phone)){
                $where['m.phone'] = $phone;
            }
            if(!empty($province)){
                $where['m.province'] = array('like','%'.$province.'%');
            }
            if(!empty($badmin)){
                $where['c.check_name'] = array('like','%'.$badmin.'%');
            }

            $orderObject = new OrdersModel();
            if(!empty($type_id)){
                $cateids = $orderObject->getCateids($type_id);
                if(!empty($cateids)){

                    $gids = $orderObject->getGoodsid($cateids);
                    //dump($cateids);
                    $gids2 = implode(",",$gids);
                    $where['c.goods_id'] = array('in', $gids2);
                }
            }
            $list = $orderObject->getOrderlist($where,1000);
            //$types = $orderObject->getTopCate();
            header("Content-type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=客户信息.csv");
            $data = "ID\t";
            $data .= "用户名\t";
            $data .= "姓名\t";
            $data .= "手机号\t";
            $data .= "课程ID\t";
            $data .= "课程\t";
            $data .= "课程费用\t";
            $data .= "已交费用\t";
            $data .= "欠款费用\t";
            $data .= "转账方式\t";
            $data .= "过期时间\t";
            $data .= "添加时间\t";
            $data .= "招生老师\t";
            $data .= "学员邮箱\t";
            $data .= "身份证号\t";
            $data .= "单位\t";
            $data .= "地址\t";
            $data .= "考区\t";
            $data .= "服务年限\t";
            $data .= "申请人\t";
            $data .= "\t\n";

            foreach ($list as $k => $v) {

                $data .= $v['id'] . "\t";
                $data .= trim($v['username']) . "\t";
                $data .= trim($v['cnname']) . "\t";
                $data .= trim($v['phone']) . "\t";
                $data .= $v['goods_id'] . "\t";
                $data .= $v['post_title'] . "\t";
                $data .= $v['course_money'] . "\t";
                $data .= $v['pay_money'] . "\t";
                $data .= ($v['course_money']-$v['pay_money']) . "\t";
                $data .= $v['payment'] . "\t";
                $expiration_time = '';
                if ($v['course_time']) {
                    $expiration_time = date('Y/m/d H:i:s', $v['course_time']);
                }
                $data .= $expiration_time . "\t";
                $data .= date('Y/m/d H:i:s', $v['create_time']) . "\t";
                $data .= $v['teacher'] . "\t";
                $data .= $v['email'] . "\t";
                $data .= "'".$v['cardnum'] . "\t";
                $data .= $v['company'] . "\t";
                $data .= $v['address'] . "\t";
                $data .= $v['province'] . "\t";
                $data .= $v['kc_year'] . "\t";
                $data .= $v['applicant'] . "\t";
                $data .= "\t\n";
            }
            echo(chr(255) . chr(254));
            echo(mb_convert_encoding($data, "UTF-16LE", "UTF-8"));
            exit;
        }
    }


    //批量修改
    public function pledit(){

        $adminname1 = $this->request->param("adminname1");
        $adminname2 = $this->request->param("adminname2");

        if(!empty($adminname1) && !empty($adminname2)){
            //原C管理员，转出方
            $admins1 = Db::name("admin")->where(array("username"=>$adminname1))->find();
            $adminid1 = $admins1['id'];
            //dump($admins1);
            //新管理员，转入方
            $admins2 = Db::name("admin")->where(array("username"=>$adminname2))->find();
            $adminid2 = $admins2['id'];
            $pid = $admins2['pid'];
            $group_id = $admins2['group_id'];

            if($group_id==5){
                $adminid3 = $adminname2;

                $result1 = Db::name("orders")->where(array("admin_id"=>$adminid1,"applicant"=>$adminname1))->find();
                $check_name1 = $result1["check_name"];
                $result = Db::name("carts")->where(array("check_name"=>$check_name1))->update(array("check_name"=>$adminid3));
            }
            else
            {
                //C管理员的审核人
                $badmin = Db::name('admin')->where(array('id' => $pid))->find();
                $adminid3 = $badmin['username'];
            }

            $where = array("o.admin_id"=>$adminid1,"o.applicant"=>$adminname1);
            $datas = array("o.admin_id"=>$adminid2,"o.applicant"=>$adminname2,"c.check_name"=>$adminid3);
            $join = [
                ["orders o","o.order_id=c.order_id"]
            ];
            $result = Db::name("carts")->alias("c")->join($join)->where($where)->update($datas);

            if($result){
                $this->success('批量修改成功',url('Orders/index'));
            }
            else
            {
                $this->error('批量修改失败');
            }
        }
        else
        {
            $group_id = Session::get("group_id");
            $teacher = $this->request->param("teacher");
            $applicant = $this->request->param("applicant");
            $checkname = $this->request->param("checkname");
            $type_id = $this->request->param('type_id');
            $username = $this->request->param('username');
            $cnname = $this->request->param('cnname');
            $goods_name = $this->request->param("goods_name");
            $phone = $this->request->param('phone');
            $province = $this->request->param("province");
            //$badmin = $this->request->param("badmin");

            $start_time = $this->request->param('start_time');
            $end_time = $this->request->param('end_time');

            $this->assign('start_time',$start_time);
            $this->assign('end_time',$end_time);

            $type = $this->request->param("type");
            $this->assign('type',$type);
            $this->assign('type_id',$type_id);

            if (!empty($type)) {
                $where['c.ck_type'] = $type;
            }

            $isqf = $this->request->param("isqf");
            $this->assign('isqf',$isqf);

            if (!empty($isqf)) {
                if($isqf==1){
                    $where['c.is_owe'] = 0;
                }
                else
                {
                    $where['c.is_owe'] = 1;
                }
            }
            $check = $this->request->param("check");
            $this->assign('check',$check);

            if (!empty($check)) {
                if($check==2){
                    $where['c.check'] = 2;
                }
                else
                {
                    $where['c.check'] = 3;
                }
            }

            $where['c.isdel'] = 0;

            if ($applicant) {
                $where['o.applicant'] = array('like','%'.$applicant.'%');
            }
            if ($checkname) {
                $where['c.check_name'] = array('like','%'.$checkname.'%');
            }
            if ($teacher) {
                $where['o.teacher'] = array('like','%'.$teacher.'%');
            }
            if(!empty($username)){
                $where['m.username'] = $username;
            }
            if(!empty($cnname)){
                $where['m.cnname'] = $cnname;
            }
            if(!empty($goods_name)){
                $where['p.post_title'] = array('like','%'.$goods_name.'%');
            }
            if(!empty($phone)){
                $where['m.phone'] = $phone;
            }
            if(!empty($province)){
                $where['m.province'] = array('like','%'.$province.'%');
            }
            if(!empty($badmin)){
                $where['c.check_name'] = array('like','%'.$badmin.'%');
            }

            if(!empty($start_time)){
                $where['c.check_time'] = array('gt',strtotime($start_time));
            }
            if(!empty($end_time)){
                $where['c.check_time'] = array('lt',strtotime($end_time));
            }
            if(!empty($start_time) && !empty($end_time)){
                $where['c.check_time'] = array('between',array(strtotime($start_time),strtotime($end_time)));
            }

            $orderObject = new OrdersModel();
            $cateids = $orderObject->getCateids($type_id);

            if(!empty($type_id)){
                if(!empty($cateids)){
                    $gids = $orderObject->getGoodsid($cateids);
                    //dump($cateids);
                    $gids2 = implode(",",$gids);
                    $where['c.goods_id'] = array('in', $gids2);
                }
            }
            $result = $orderObject->getOrderlist($where,20);
            //dump($orderObject->getLastSql());

            $ids = $orderObject->getAccount('',2,'id');
            $admin_id = cmf_get_current_admin_id();
            array_push($ids,$admin_id);

            if($group_id != 1){
                $where['m.admin_id'] = array('IN',trim(implode(',',$ids),','));
            }
            $this->assign("wherestr",json_encode($where));

            if ($this->request->isPost()) {

                $course_time = $this->request->param('course_time');
                if(empty($course_time)){
                    $this->error('请填写课程结束时间');
                }
                $where = $this->request->param("wherestr");
                $where = htmlspecialchars_decode($where);
                $where = json_decode($where,true);
              if(!empty($where)){
                  $join = [
                    ["orders o","o.order_id=c.order_id"]
                  ];
                  $res = Db::name('carts')->alias('c')->join($join)->where($where)->update(array('c.course_time'=>strtotime($course_time)));
                  if($res){
                      $this->success('批量修改成功'.$res.'条数据',url('Orders/index'));
                  }else{
                      $this->error('批量修改失败');
                  }
                  exit();
               }
                else
                {
                    $this->error('缺少修改条件！');
                }
            }
        }
        $this->assign('group_id',$group_id);//用户组ID
        $this->assign('result', $result);
        $this->assign('page', $result->render());
        return $this->fetch();
    }

//学员补费
    public function add_bufei(){

        $group_id = Session::get("group_id");

        if ($this->request->isPost()) {

            $cid = $this->request->param('cid');
            $price = $this->request->param('price');
            $username = $this->request->param('username');
            $payment = $this->request->param('payment');
            $marks = $this->request->param('remarks');
            $paytime = $this->request->param("paytime");
            $do = $this->request->param("do");
            $bid = $this->request->param("bid");

            if(empty($cid) || empty($price)){
                $this->error('参数错误');
            }

            $save_data = array(
                'orderid'=>$cid,
                'price'=>$price,
                'username'=>$username,
                'payment'=>$payment,
                'marks'=>$marks,
                'status'=>0,
                'datetime'=>$paytime
            );

            $updata = new Upload();

            if(request()->file('pay_pic1')){
                $uploads = $updata->uploadpic('pay_pic1');
                if ($uploads['res']) {
                    $save_data['picurl1'] = $uploads['fileurl'];
                }
            }

            if(request()->file('pay_pic2')){
                $uploads = $updata->uploadpic('pay_pic2');
                if ($uploads['res']) {
                    $save_data['picurl2'] = $uploads['fileurl'];
                }
            }

            if($do=='edit' && !empty($bid)){
                $result = Db::name('bufeis')->where(array('id'=>$bid))->update($save_data);
                if($result){
                    $this->success('保存成功',url('Orders/add_bufei',array("cid"=>$cid)));
                }else{
                    $this->error('保存失败');
                }
            }
            else
            {
                if(Db::name('bufeis')->insert($save_data)){
                    $this->success('保存成功',url('Orders/add_bufei',array("cid"=>$cid)));
                }else{
                    $this->error('保存失败');
                }
            }
        }
        else
        {
            $bid = $this->request->param("bid");
            $cid = $this->request->param("cid");
            $do = $this->request->param("do");
            if(!empty($bid) && $do=="edit"){
                $bufei_data = Db::name("bufeis")->where(array("id"=>$bid))->find();
                $this->assign('bufei_data',$bufei_data);
            }

            if(!empty($bid) && $do=="del"){
                $data['status'] = -1;
                Db::name("bufeis")->where(array("id"=>$bid))->update($data);
                $this->success('删除成功');
            }

            $wherec = array("c.id"=>$cid);
            $orderObject = new OrdersModel();
            $userdata = $orderObject->getOrderlist($wherec,0);
            $this->assign('userdata',$userdata);

            $where["b.orderid"] = $cid;
            $where['b.status'] = ["gt",-1];
            $join = [
              ["carts c","c.id=b.orderid"],
              ["member m","m.id=c.member_id"]
            ];
            $field = "b.*,m.username";
            $dataList = Db::name("bufeis")->alias("b")->join($join)->field($field)->where($where)->select();
            $this->assign('dataList',$dataList);
            $this->assign('cid',$cid);
            $this->assign("group_id",$group_id);
            $this->assign('do',$do);
            return $this->fetch();
        }
    }


    public function bufeilist(){

        $group_id = Session::get("group_id");
        $admin_id = Session::get('admin_id');
        $username = Session::get('username');

        $bid = $this->request->param("bid");
        $orderid = $this->request->param("orderid");
        $where = [];
        $do = $this->request->param("do");

        $where['b.status'] = array("gt",-1);

        if(!empty($bid) && $do=="edit"){
            $bufei_data = Db::name("bufeis")->where(array("id"=>$bid))->find();
            $this->assign('bufei_data',$bufei_data);
        }

        if(!empty($bid) && $do=="exam"){
            $data['status'] = 1;
            Db::name("bufeis")->where(array("id"=>$bid))->update($data);
        }

        if($group_id == 6){
            $where = array('o.admin_id' => $admin_id);
        }

        if($group_id == 5){
            $where = array('c.check_name' => $username);
        }

        $join = [
            ["carts c","c.id=b.orderid"],
            ["orders o","c.order_id=o.order_id"],
            ["member m","m.id=c.member_id"]
        ];
        $field = "b.*,m.username,o.admin_id,o.member_id,c.goods_id,o.applicant,c.check_name";

        $dataList = Db::name("bufeis")->alias("b")->join($join)->field($field)
            ->where($where)
            ->order("b.id desc")
            ->select();

        $this->assign('dataList',$dataList);
        $this->assign('orderid',$orderid);
        $this->assign("group_id",$group_id);
        $this->assign('do',$do);
        return $this->fetch();
    }


    public function bufei_del(){

        $bid = $this->request->param("bid");
        $where = array("id"=>$bid);

        if(!empty($bid)){
            $data['status'] = -1;
            Db::name("bufeis")->where($where)->update($data);
            $this->success('删除成功');
        }
        else
        {
            $this->error('参数不足！');
            exit();
        }
    }


    public function bufei_sh(){
        if ($this->request->isPost()) {

                $group_id = Session::get("group_id");
                $admin_id = Session::get('admin_id');
                $username = Session::get('username');

                $bid = $this->request->param("bid");
                $cid = $this->request->param("cid");
                $course_time = $this->request->param("course_time");
                $check = $this->request->param("check");
                $where = [];
                $result = 0;
                $is_owe = 0;

                if (empty($cid) || empty($bid) || empty($group_id)) {
                    $this->error('参数不足！');
                    exit();
                } elseif($check == 2) {
                    $res = Db::name("bufeis")->where(array("id" => $bid))->find();
                    $price = $res["price"];
                    if (!empty($price)) {
                        $res2 = Db::name("carts")->where(array("id" => $cid))->find();
                        $bufei0 = $res2['course_money'];    //成交价
                        $bufei1 = $res2['pay_money'];    //已缴费
                        $bufei2 = $bufei1 + $price;        //补费后已缴费

                        if ($bufei2 > $bufei0) {
                            $result2 = Db::name("bufeis")->where(array("id" => $bid))->update(array("status" => 2));
                            $this->error('补费金额不正确，已拒绝通过！');
                        } elseif ($bufei2 < $bufei0) {
                            $is_owe = 1;
                        } else {
                            $is_owe = 0;
                        }

                        $bufei_time = time();
                        $data = array(
                            "pay_money" => $bufei2,
                            "is_owe" => $is_owe,
                            "owe_time" => $bufei_time,
                            "course_time" => strtotime($course_time)
                        );
                        if (($group_id == 5 && $username == $res2['check_name']) || $group_id == 1 || $group_id == 3) {
                            $result = Db::name("carts")->where(array("id" => $cid))->update($data);
                            if ($result) {
                                $result2 = Db::name("bufeis")->where(array("id" => $bid))->update(array("status" => 1));
                                $active_name = Session::get('username');
                                $this->cource_log($cid,$active_name,"学员订单补费");
                                $this->success('审核成功', url('Orders/bufeilist'));
                            } else {
                                $this->error('审核失败');
                            }
                        } else {
                            $this->error('您的权限不足！');
                        }
                    } else {
                        $this->error('数据不完整！');
                    }
                }
                else
                {
                    if($check == 3){
                        $result2 = Db::name("bufeis")->where(array("id" => $bid))->update(array("status" => 2));
                        if($result2){
                            $this->success('审核已拒绝通过!', url('Orders/bufeilist'));
                        }
                    }
                }
        }
        else
        {
            $bid = $this->request->param("bid");
            $cid = $this->request->param("cid");
            $join = [
                ["carts c","c.id=b.orderid"],
                ["orders o","c.order_id=o.order_id"],
                ["member m","m.id=c.member_id"]
            ];
            $field = "b.*,m.username,o.admin_id,o.member_id,c.goods_id,o.applicant,c.course_time";
            $where = array("b.id" => $bid);
            $res2 = Db::name("bufeis")->alias("b")->join($join)->field($field)->where($where)->find();
            $this->assign('applicant',$res2['applicant']);
            $this->assign('res2',$res2);
            $this->assign('bid',$bid);
            $this->assign('cid',$cid);
            return $this->fetch();
        }
    }


    //开课续学信息
    public function continuer()
    {

        if ($this->request->isAjax()) {
            $page = $this->request->param("page");
            $keyword = $this->request->param("keyword");
            $orderObject = new OrdersModel();
            $goods_type = $this->request->param("kcid");
            $goods_data = $orderObject->getGoodslist(20, $page,$keyword,$goods_type);
            $pages = $goods_data->render();
            $tempstr = '';
            foreach ($goods_data as $vo) {
                $tempstr = $tempstr . '<tr><td>' . $vo["id"] . '</td><td>' . $vo["post_title"] . '</td><td>' . date("Y-m-d H:i:s", $vo["create_time"]) . '</td><td><input name="goods_id" class="goods_id" type="checkbox" value="' . $vo['id'] . '" data-name="' . $vo['post_title'] . '" /></td></tr>';
            }
            //dump($goods_data);
            $gogdslist['pages'] = $pages;
            $gogdslist['goods_data'] = $tempstr;
            return json($gogdslist);
        }
        $id = $this->request->param('cid');
        if (empty($id)) {
            $this->error('参数错误');
        }
        $orderObject = new OrdersModel();
        $where = array('c.id' => $id);
        $data = $orderObject->getOrderlist($where, 0);;
        if (empty($data)) {
            $this->error('暂无课程订单数据');
        }

        $groupid = Session::get('group_id');

        if ($this->request->isPost()) {

            $old_id = $this->request->param('id');      //原来的订单ID
            $order_id = $this->request->param('order_id');    //订单编号
            $goods_id = $this->request->param('goods_id');    //课程id
            $pay_money0 = $this->request->param('pay_money/a');    //已付费用
            $pay_money = $pay_money0[0];
            $course_money0 = $this->request->param('course_money/a');  //课程费用
            $course_money = $course_money0[0];
            $course_time = $this->request->param('course_time');    //课程到期时间
            $owe_time = $this->request->param('owe_time');  //补费时间
            $kc_type0 = $this->request->param('kc_type/a');    //课程年限
            $kc_type = $kc_type0[0];
            $kc_price0 = $this->request->param('kc_price/a');  //课程价格
            $kc_price = $kc_price0[0];
            $remarks = $this->request->param('remarks');    //摘要
            $payment = $this->request->param('payment');    //支付方式
            $member_id = $this->request->param('member_id');    //会员编号
            $check_name = $this->request->param('check_name');   //审核人
            $admin_id = $this->request->param("admin_id");      //申请人编号
            $teacher = $this->request->param("teacher");        //招生老师
            $applicant = $this->request->param('applicant');    //原申请人
            $applicant0 = $this->request->param('applicant0');  //新申请人
            $do = $this->request->param('do');      //续学xuxue/转课zhuan
            $tempstr = getTempstr($do);
            $this->assign("tempstr", $tempstr);

            if (empty($goods_id)) {
                $this->error('课程错误');
                exit();
            }
            $goods = Db::name('portal_post')->where(array('id' => $goods_id))->find();
            if (empty($goods)) {
                $this->error('课程错误');
                exit();
            }

            $updata = new Upload();

            if (request()->file('result') && $do == 'xuxue') {
                $uploads = $updata->uploadpic('result');
                if ($uploads['res']) {
                    $result_data['result'] = $uploads['fileurl'];
                    $result_data['isexam'] = 1;
                }
            }
            //上传文件
            if ($do == 'xuxue' || $do == 'zhuan') {
                $result_data['status'] = 0; //关闭原来的课程
                $result_id = Db::name('carts')->where(array('id' => $old_id, 'status' => 1))->update($result_data);
                if ($result_id) {
                    $active_name = Session::get('name');
                    $this->cource_log($old_id, $active_name, "学员" . $tempstr . "关闭原课程");
                }
            }

            $wh2 = array(
                'goods_id' => $goods_id,
                'member_id' => $member_id,
                'status' => 1
            );
            if ($do == 'xuxue') {
                $wh2["course_time"] = ["lt", time()];
            }
            if ($do == 'zhuan') {
                    $wh2["course_time"] = ["gt", time()];
             }
                $course_order = Db::name('carts')->where($wh2)->find();
                if (!empty($course_order)) {
                    $this->error('课程已经存在!');
                    exit();
                }

                if (empty($course_money) || empty($pay_money)) {
                    $type = 3;
                } else {
                    if (($course_money / $kc_price) < 0.8) {
                        $type = 2;
                    } else {
                        $type = 1;
                    }
                }

                if ($pay_money < $course_money) {
                    $isowe = 1;
                } else {
                    $isowe = 0;
                }

                if ($pay_money > $course_money) {
                    $this->error('支付金额不能大于成交金额');
                }
                if (empty($course_money)) {
                    // $this->error('成交金额不能为空');
                }

                $save_data = array(
                    'order_id' => $order_id,
                    'goods_id' => $goods_id,
                    'pay_money' => $pay_money,
                    'course_money' => $course_money,
                    'course_time' => strtotime($course_time),
                    'owe_time' => strtotime($owe_time),
                    'kc_year' => $kc_type,
                    'kc_price' => $kc_price,
                    'member_id' => $member_id,
                    'check_name' => $check_name,
                    'is_owe' => $isowe,
                    'ck_type' => $type,
                    'check' => 1,
                    'isdel' => 0,
                    'create_time' => time()
                );

                if ($groupid == 6) {
                    $save_data['check'] = 1;
                    $save_data['check_note'] = '';
                }

                if (Db::name('carts')->insert($save_data)) {
                    $this->success('提交成功', url('Orders/index'));
                } else {
                    $this->error('操作失败');
                }
                exit();

            }
            $qfje = $data['course_money'] - $data['pay_money'];
            if ($qfje > 0) {
                $data['qf'] = 1;
                $data['qfje'] = $qfje;
            } else {
                $data['qf'] = 0;
                $data['qfje'] = '未欠费';
            }

        $info= Db::name('kecheng_cate')->field('id,parentid,parentstr,classname,level')->select();
        $info = json_decode($info,true);
        $info = unlimitedForLevel($info);
        //dump($info);
        $this->assign('info',$info);

            $do = $this->request->param('do');      //续学xuxue/转课zhuan
            $tempstr = getTempstr($do);
            $this->assign("tempstr", $tempstr);
            $this->assign('do', $do);
            //获取课程信息
            $orderObject = new OrdersModel();
            $goods_data = $orderObject->getGoodslist();
            $pages = $goods_data->render();
            $this->assign('pages', $pages);
            $this->assign('goods_data', $goods_data);
            $this->assign('data', $data);
            $this->assign('group_id', $groupid);
            $this->assign('session_groupid', $groupid);
            $cadminlist = Db::name("admin")
                ->where(array("group_id" => 6))
                ->field("id,username")
                ->select();
            $this->assign('cadminlist', $cadminlist);
            //dump($data);
            return $this->fetch();
     }

    //休学
    public function xiuxue(){

        if ($this->request->isPost()) {

            $id = $this->request->param('id');
            $cid = $this->request->param('cid');
            $xiu_note = $this->request->param('xiu_note');
            $check = $this->request->param('check');

            $active_name = Session::get('name');

            if(empty($id) && $check == 1)       //休学处理
            {
                if(empty($cid)){
                    $this->error('参数错误');
                }
                if(empty($xiu_note)){
                    $this->error('请输入休学原因');
                }

                $order = Db::name('carts')->where(array("id"=>$cid))->find();
                if(empty($order)){
                    $this->error('暂无数据');
                }
            }
            else        //复学处理
            {
                $order = Db::name('xiuxues')->where(array("orderid"=>$id))->find();
                if(empty($order)){
                    $this->error('暂无数据');
                }
            }

            if($check == 2){
                $xiu_status = 3;
                $xiu_str = '复学申请成功！';
                $xiu_str2 = '复学申请失败！';

                $save_data = array(
                    'xiu_node2'=>$xiu_note,
                    'xiu_status'=>$xiu_status,
                    'xiu_time2'=>date("Y-m-d h:i:s",time())
                );

                if(Db::name('xiuxues')->where(array("orderid"=>$id))->update($save_data)){
                    $this->cource_log($id,$active_name,"提交复学申请");
                    Db::name('carts')->where(array("id"=>$id))->update(array("xiu_status"=>$xiu_status));
                    $this->success($xiu_str,url("Orders/xiuList"));
                }else{
                    $this->error($xiu_str2,url("Orders/xiuList"));
                }
                exit();
            }
            else
            {
                $xiu_status = 1;
                $xiu_str = '休学申请成功！';
                $xiu_str2 = '休学申请失败！';

                $save_data = array(
                    'orderid'=>$cid,
                    'xiu_node'=>$xiu_note,
                    'xiu_status'=>$xiu_status,
                    'xiu_time'=>date("Y-m-d h:i:s",time())
                );

                if(Db::name('xiuxues')->insert($save_data)){
                    $this->cource_log($id,$active_name,"提交休学申请");
                    Db::name('carts')->where(array("id"=>$cid))->update(array("xiu_status"=>$xiu_status));
                    $this->success($xiu_str,url("Orders/xiuList"));
                }else{
                    $this->error($xiu_str2,url("Orders/xiuList"));
                }
                exit();
            }
        }
        else
        {
            $xiu = $this->request->param('xiu');
            $id = $this->request->param('id');
            $cid = $this->request->param('cid');

            if(empty($id))       //休学处理
            {
                $xiuxues = Db::name('carts')->where(array('id' => $cid))->find();
            }
            else
            {
                $xiuxues = Db::name('xiuxues')->where(array('id' => $id))->find();
            }
            $this->assign('xiuxues',$xiuxues);
            $this->assign('id',$id);
            $this->assign('cid',$cid);
            $this->assign('xiu',$xiu);
            return $this->fetch();
        }
    }

    //休学列表
    public function xiuList(){
        $username = $this->request->param('username');
        $admin_id = cmf_get_current_admin_id();
        $group_id = Session::get("group_id");
        $where = array();
        if ($username) {
            $where['m.username'] = array('like','%'.$username.'%');
        }
        $orderObject = new OrdersModel();
        $ids = $orderObject->getAccount('',2,'id');
        array_push($ids,$admin_id);
        if($group_id != 1){
            $where['o.admin_id'] = array('IN',trim(implode(',',$ids),','));
        }
        $where['x.xiu_status'] = array('neq',0);
        $join = [
            ["carts c","c.id=x.orderid"],
            ["orders o","o.order_id=c.order_id"],
            ["member m","m.id=c.member_id"],
            ["portal_post p","p.id=c.goods_id"]
        ];
        $field = "x.*,m.username,m.phone,o.applicant,c.create_time,c.goods_id,p.post_title,c.course_money,c.pay_money,c.status,c.xiu_status as cxiu_status";
        $order = Db::name('xiuxues')->alias('x')->join($join)->field($field)->where($where)->order('x.id DESC ')->paginate(20);
        $this->assign('username', $username);
        $this->assign('order', $order);
        $this->assign('page', $order->render());// 赋值分页输出
        return $this->fetch();
    }

    //休学操作，xiu_status值为1，休学申请，需要审核，2休学审核通过，3复学申请，4复学审核通过，-1拒绝通过复学，0拒绝休学
    public function xiustatus(){

        if ($this->request->isPost()) {

            $id = $this->request->param('id');
            $cid = $this->request->param('cid');
            $xiuxue_note = $this->request->param('xiuxue_note');
            $check = $this->request->param('check_id');
            $xiu_status = $this->request->param("xiu_status");
            $active_name = Session::get('name');

            if(empty($id) || empty($cid)){
                $this->error('参数错误');
            }
            if(empty($xiuxue_note)){
                $this->error('请输入审核意见');
            }
            $order = Db::name('carts')->find($cid);
            if(empty($order)) {
                $this->error('暂无数据');
            }
            if($order['check'] !=2){
                $this->error('该课程状态不支持休学');
            }
            if($xiu_status=='1'){
                if($check == '2'){   //休学审核
                    $status = 0;
                    $xiucode = 2;
                }
                else
                {
                    $status = 1;
                    $xiucode = 1;
                }
                $save_data = array(
                    'remark'=>$xiuxue_note,
                    'xiu_status' => $xiucode,
                    'check_time'=>date("Y-m-d h:i:s",time())
                );
                $strtemp = "休学审核";
            }
            else
            {
                if($check == '2'){   //复学
                    $status = 1;
                    $xiucode = 4;
                }
                else
                {
                    $status = 0;
                    $xiucode = 3;
                }
                $save_data = array(
                    'remark2'=>$xiuxue_note,
                    'xiu_status' => $xiucode,
                    'check_time2'=>date("Y-m-d h:i:s",time())
                );
                $strtemp = "复学审核";
            }

            if(Db::name('xiuxues')->where(array('id'=>$id))->update($save_data)){
                $order_data = array(
                    'xiu_status' => $xiucode,
                    'status'=>$status
                );
                Db::name('carts')->where(array('id'=>$cid))->update($order_data);
                $this->cource_log($id,$active_name,$strtemp);
                $this->success('审核成功',url("Orders/xiuList"));
            }else{
                $this->error('审核失败',url("Orders/xiuList"));
            }
            exit();
        }
        else
        {
            $id = $this->request->param('id');
            $cid = $this->request->param('cid');
            $check = $this->request->param('check');
            $this->assign('id',$id);
            $this->assign('cid',$cid);
            $this->assign('check',$check);
            return $this->fetch();
        }
    }

    //退课申请
    public function tuike(){

        $cid = $this->request->param('cid');
        if ($this->request->isPost()) {

            $tk_note = $this->request->param('tk_note');
            if(empty($cid)){
                $this->error('参数错误');
            }
            if(empty($tk_note)){
                $this->error('请输入退课原因');
            }
            $order = Db::name('carts')->where(array("id"=>$cid))->find();
            if(empty($order)){
                $this->error('暂无数据');
                exit();
            }
            if(!empty($order['tk_status'])){
                $this->error('课程状态不允许退课');
            }
            $save_data = array(
                'tk_status'=>1,
                'status'=>0
            );
            if(Db::name('carts')->where(array('id'=>$cid))->update($save_data)){
                $istuike = Db::name("tuikes")->where(array('cid'=>$cid))->find();
                if(empty($istuike)){
                    $tk_data['cid'] = $cid;
                    $tk_data['tk_status'] = 1;
                    $tk_data['datetime'] = time();
                    $tk_data['tk_note'] = $tk_note;
                    Db::name('tuikes')->insert($tk_data);
                }
                $active_name = Session::get('name');
                $this->cource_log($cid,$active_name,"申请退课");
                $this->success('退课申请成功',url('Orders/tkList'));
            }else{
                $this->error('退课申请失败',url('Orders/index'));
            }
            exit();
        }
        $this->assign('cid',$cid);
        return $this->fetch();
    }

    //退课列表
    public function tkList(){
        $username = $this->request->param('username');
        $admin_id = cmf_get_current_admin_id();
        $group_id = Session::get("group_id");
        $where = array();
        if ($username) {
            $where['m.username'] = array('like','%'.$username.'%');
        }
        $orderObject = new OrdersModel();
        $ids = $orderObject->getAccount('',2,'id');
        array_push($ids,$admin_id);
        if($group_id != 1){
            $where['o.admin_id'] = array('IN',trim(implode(',',$ids),','));
        }
        $where['t.tk_status'] = ["gt",0];
        $join = [
            ["carts c","c.id=t.cid"],
            ["orders o","o.order_id=c.order_id"],
            ["member m","m.id=c.member_id"],
            ["portal_post p","p.id=c.goods_id"]
        ];
        $field = 't.*,c.id as cid,c.check,c.goods_id,p.post_title,c.course_money,c.pay_money,o.applicant,m.username,m.phone,c.course_time,m.card_face,o
        .pay_pic';
        $order = Db::name('tuikes')->alias("t")->join($join)->field($field)->where($where)->order('t.id DESC ')->paginate(20);
        $this->assign('username', $username);
        $this->assign('order', $order);
        $this->assign('page', $order->render());// 赋值分页输出
        return $this->fetch();
    }

    //退课审核
    public function tkstatus(){

        $id = $this->request->param('id');
        $cid = $this->request->param('cid');
        $check = $this->request->param('check');
        if ($this->request->isPost()) {

            $admin_id = cmf_get_current_admin_id();

            $tkstatus_note = $this->request->param('tkstatus_note');
            $check_id = $this->request->param('check_id');
            if(empty($check)){
                $this->error('请先审核');
            }
            if(empty($id)){
                $this->error('参数错误');
            }
            if(empty($tkstatus_note)){
                $this->error('请输入退课原因');
            }
            if($check !=2){
                $this->error('该课程状态不支持退课');
            }
            $tkdata = Db::name('tuikes')->find($id);
            if(empty($tkdata)) {
                $this->error('暂无数据');
            }
            $where = array("c.id"=>$cid);
            $orderObject = new OrdersModel();
            $order = $orderObject->getOrderlist($where,0);

            $save_data = array(
                'tkstatus_note'=>$tkstatus_note,
                'tk_status'=>$check,
                'checktime'=>time()
            );
            if($check_id == 2){
                $a_model = Db::name('admin');
                $adata = $a_model->where(array('id'=>$order['admin_id']))->find();
                if(empty($adata)){
                    $uid = $admin_id;
                }else{
                    if(empty($adata['pid'])){
                        $uid = $admin_id;
                    }else{
                        $uid = $adata['pid'];
                    }
                }
                $acc_model = new AccountsModel();
                $result=$acc_model->add_accounts($uid,$order['course_money'],3,$id);
                if($result['code']==500){
                    $this->error($result['msg'],url('Orders/index'));
                    exit;
                }
            }
            if(Db::name('tuikes')->where(array('id'=>$id))->update($save_data)){
                $active_name = Session::get('name');
                $this->cource_log($id,$active_name,"审核退课");
                $this->success('审核成功');
            }else{
                $this->error('审核失败');
            }
            exit();
        }
        return $this->fetch();
    }

}
