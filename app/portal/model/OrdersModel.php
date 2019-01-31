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
namespace app\portal\model;

use think\Model;
use think\Db;

class OrdersModel extends Model
{
    //获取订单列表
    public function getOrderlist($where=array(),$limit=0){
        if(empty($where)){
            $where['c.isdel'] = 0;
        }
        $join = [
            ["orders o","o.order_id = c.order_id"],
            ["member m","o.member_id = m.id"],
            ["portal_post p","p.id = c.goods_id"]
        ];
        $field = "c.*,c.id as cid,o.id as oid,o.remarks,o.payment,o.pay_pic,o.applicant,o.teacher,o.agreement,o.upload_file,p.post_title,m.username,m.cnname,m.phone,m.cardnum,m.email,m.province,m.address,m.company,m.card_face,m.card_back";
        if($limit>0){
            $data = Db::name("carts")->alias("c")->join($join)->field($field)->where($where)->order("o.id DESC")->paginate($limit);
        }
        else
        {
            $data = Db::name("carts")->alias("c")->join($join)->field($field)->where($where)->find();
        }
        return $data;
    }

    //获取管理员信息
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

    //获取所有顶级项目
    public function getTopCate(){

        $goodscate = Db::name('portal_category')->where(array('parent_id'=>0))->select();
        return $goodscate;
    }

    //根据项目id获取所有子项目
    public function getCateids($types=0){
        $ids = [];
        $typeids = Db::name("portal_category")->field("id")->where(array("parent_id"=>$types))->select();
        foreach($typeids as $vo){
            $cateid = $vo["id"];
            $typeids2 = Db::name("portal_category")->field("id")->where(array("parent_id"=>$cateid))->select();
            foreach($typeids2 as $vo2){
                $cateid2 = $vo2["id"];
                 array_push($ids,$cateid2);
            }
        }
        return $ids;
    }

    //根据项目id获取goodsid
    public function getGoodsid($cateids){

        $gids = [];
        $cateids2 = implode(",",$cateids);
        $where["category_id"] = array("in",$cateids2);
        $result = Db::name("portal_category_post")->where($where)->select();
        foreach($result as $vo){
            array_push($gids,$vo["post_id"]);
        }
        return $gids;
    }

    //获取所有课程列表
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


    public function send_email($nickname, $from, $password, $to, $subject = '', $content = '', $Attachment='')
    {
        //require('../tp5/Library/Vendor/phpmailer/class.phpmailer.php');
        // 实例化PHPMailer核心类
        $mail = new \PHPMailer();
        // 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $mail->SMTPDebug = 1;
        // 使用smtp鉴权方式发送邮件
        $mail->isSMTP();
        // smtp需要鉴权 这个必须是true
        $mail->SMTPAuth = true;
        // 链接qq域名邮箱的服务器地址
        $mail->Host = 'smtp.qq.com';
        // 设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';
        // 设置ssl连接smtp服务器的远程服务器端口号
        $mail->Port = 465;
        // 设置发送的邮件的编码
        $mail->CharSet = 'UTF-8';
        // 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = $nickname;
        // smtp登录的账号 QQ邮箱即可
        $mail->Username = $from;
        // smtp登录的密码 使用生成的授权码
        $mail->Password = $password;
        // 设置发件人邮箱地址 同登录账号
        $mail->From = $from;
        // 邮件正文是否为html编码 注意此处是一个方法
        $mail->isHTML(true);
        // 设置收件人邮箱地址
        $mail->addAddress($to);
        // 添加多个收件人 则多次调用方法即可
        //$mail->addAddress('87654321@163.com');
        // 添加该邮件的主题
        $mail->Subject = $subject;
        // 添加邮件正文
        $mail->Body = $content;
        // 为该邮件添加附件
        if(!empty($Attachment)){
            $mail->addAttachment($Attachment);
        }
        // 发送邮件 返回状态
        $status = $mail->send();
        return $status;
    }

    /* 处理上传exl数据
    * $file_name  文件路径
    */
    public function import_exl($path){

        $objPHPExcel = new \PHPExcel();
        //文件的扩展名
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext == 'xlsx') {
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($path, 'utf-8');
        } elseif ($ext == 'xls') {
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($path, 'utf-8');
        }

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        $ar = array();
        $i = 0;
        $importRows = 0;
        $data=array();
        for($i=1;$i<$highestRow+1;$i++){
            $username = (string)$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getValue();
            $phone = (string)$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue();
            $goods_id = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getValue();
            $goods_name = (string)$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getValue();
            $course_money = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue();
            $pay_money = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getValue();
            $pay_way = (string)$objPHPExcel->getActiveSheet()->getCell('J'.$i)->getValue();
            $tms = \PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell('L'.$i)->getValue());
            $course_time = \PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell('K'.$i)->getValue());
            $kc_type = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getValue();
            $student_email = (string)$objPHPExcel->getActiveSheet()->getCell('N'.$i)->getValue();
            $zhaosheng_teacher = (string)$objPHPExcel->getActiveSheet()->getCell('M'.$i)->getValue();
            $applicant = (string)$objPHPExcel->getActiveSheet()->getCell('P'.$i)->getValue();

            //if(is_object($applicant))  $applicant= $applicant->__toString();

            $data[]=array(
                'username'=>$username,
                'phone'=>$phone,
                'goods_id'=>$goods_id,
                'post_title'=>$goods_name,
                'course_money'=>$course_money,
                'pay_money'=>$pay_money,
                'payment'=>$pay_way,
                'time'=>$tms,
                'course_time'=>$course_time,
                'kc_year'=>$kc_type,
                'email'=>$student_email,
                'teacher'=>$zhaosheng_teacher,
                'applicant'=>$applicant
            );
        }
        return $data;
    }

    public function getAdminid($applicant){
        $where["username"] = $applicant;
        $adminData = Db::name("admin")->where($where)->find();
        $pid = $adminData['pid'];
        $groupid = $adminData['group_id'];
        $data["id"] = $adminData['id'];
        $data["pid"] = $pid;
        $data["group_id"] = $groupid;
        if($groupid == 6){
            $shenhe = Db::name("admin")->where(array("id"=>$pid))->find();
            $data["shenher"] = $shenhe["username"];
        }
        else
        {
            $data["shenher"] = $applicant;
        }
        return $data;
    }


}