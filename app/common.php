<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/25 0025
 * Time: 12:00
 */

//判断是否是IP
function isIp($ip){
    $reg = "/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))\.){3}((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))$/";
    if(preg_match($reg,$ip)){
        return true;
    }else{
        return false;
    }
}
//判断是否是手机号
function isPhone($phone){
    $reg = "/^1[3|4|5|6|7|8|9][0-9]\d{8}$/";
    if(preg_match($reg,$phone)){
        return true;
    }else{
        return false;
    }
}
//判断是否是邮箱
function isEmail($email){
    $reg = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
    if(preg_match($reg,$email)){
        return true;
    }else{
        return false;
    }
}

//将数组的指定的值变成键
function arrayValueToKey($arr = array(),$value='id'){
    $new_arr = [];
    if(!is_array($arr)){
        return false;
    }
    foreach($arr as $key=>$val){
        $new_arr[$val[$value]] = $val;
    }
    return $new_arr;
}

//发送验证码短信
function sendMsg($phone,$type =1){
    $verify = rand(100000,999999);
    $flag = 0;
    $params='';//要post的数据
    $argv = array(
        'name'=>'acjy',     //必填参数。用户账号
        'pwd'=>'2D676E307B841FD3D22ECF045670',     //必填参数。（web平台：基本资料中的接口密码）
        //'content'=>'短信验证码为：'.$verify.'，请勿将验证码提供给他人。',   //必填参数。发送内容（1-500 个汉字）UTF-8编码
        //'content'=>$msg,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
        'content'=>'短信验证码为：'.$verify.'，请勿将验证码提供给他人。',   //必填参数。发送内容（1-500 个汉字）UTF-8编码
        'mobile'=>$phone,   //必填参数。手机号码。多个以英文逗号隔开
        'stime'=>'',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
        'sign'=>'奥创百科',    //必填参数。用户签名。
        'type'=>'pt',  //必填参数。固定值 pt
        'extno'=>''    //可选参数，扩展码，用户定义扩展码，只能为数字
    );

    foreach ($argv as $key=>$value) {
        if ($flag!=0) {
            $params .= "&";
            $flag = 1;
        }
        $params.= $key."="; $params.= urlencode($value);// urlencode($value);
        $flag = 1;
    }
    $url = "http://210.5.152.195:1860/asmx/smsservice.aspx?".$params; //提交的url地址
    $con= substr( file_get_contents($url), 0, 1 );  //获取信息发送后的状态
    if($con == '0'){

        if($type ==1) {
            session('verify', $verify);
            return true;
        }else{
            if(M('msg_code')->add(array('phone'=>$phone,'create_time'=>date('Y-m-d H:i:s'),'msg_code'=>$verify))){
                return true;
            }
        }
    }
    else{
        return false;
    }

}

//发送自定义内容短信
function sendMsg2($phone,$content){

    $flag = 0;
    $params='';//要post的数据
    $argv = array(
        'name'=>'acjy',     //必填参数。用户账号
        'pwd'=>'2D676E307B841FD3D22ECF045670',     //必填参数。（web平台：基本资料中的接口密码）
        //'content'=>'短信验证码为：'.$verify.'，请勿将验证码提供给他人。',   //必填参数。发送内容（1-500 个汉字）UTF-8编码
        //'content'=>$msg,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
        'content'=>$content,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
        'mobile'=>$phone,   //必填参数。手机号码。多个以英文逗号隔开
        'stime'=>'',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
        'sign'=>'奥创百科',    //必填参数。用户签名。
        'type'=>'pt',  //必填参数。固定值 pt
        'extno'=>''    //可选参数，扩展码，用户定义扩展码，只能为数字
    );

    foreach ($argv as $key=>$value) {
        if ($flag!=0) {
            $params .= "&";
            $flag = 1;
        }
        $params.= $key."="; $params.= urlencode($value);// urlencode($value);
        $flag = 1;
    }
    $url = "http://210.5.152.195:1860/asmx/smsservice.aspx?".$params; //提交的url地址
    $con= substr( file_get_contents($url), 0, 1 );  //获取信息发送后的状态
    if($con == '0'){
        return true;
    }
    else{
        return false;
    }

}


function send_email($nickname, $from, $password, $to, $subject = '', $content = '', $Attachment='')
{
    //require('./Vendor/phpmailer/phpmailer/src/PHPMailer.php');
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
    $mail->addAttachment($Attachment);
    // 发送邮件 返回状态
    $status = $mail->send();
}


function jsonOut($code,$data){
    return json(array("code"=>$code,'data'=>$data));
}

//随机数字符
function randomStr(){
    $arr = array_merge(range(0,9),range('A','Z'));
    $str = '';
    $arr_len = count($arr);
    for($i = 0;$i < 8;$i++){
        $rand = mt_rand(0,$arr_len-1);
        $str.=$arr[$rand];
    }
    return $str;
}

//生成订单号
function getOrderCode(){
    $ordercode = "A".date("Ymdhis").rand(1000,9999);
    return $ordercode;
}

function RMB_Upper($num)
{
    $num = round($num,2);  //取两位小数
    $num = ''.$num;  //转换成数字
    $arr = explode('.',$num);
    $str_left = $arr[0]; // 12345
    if(isset($arr[1])){
        $str_right = $arr[1]; // 67
    }
    else
    {
        $str_right = "00";
    }

    $len_left = strlen($str_left); //小数点左边的长度
    $len_right = strlen($str_right); //小数点右边的长度

    //循环将字符串转换成数组，
    for($i=0;$i<$len_left;$i++)
    {
        $arr_left[] = substr($str_left,$i,1);
    }
    //print_r($arr_left);
    //output:Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 )

    for($i=0;$i<$len_right;$i++)
    {
        $arr_right[] = substr($str_right,$i,1);
    }
    //print_r($arr_right);
    //output：Array ( [0] => 6 [1] => 7 )

    //构造数组$daxie
    $daxie = array(
        '0'=>'零',
        '1'=>'壹',
        '2'=>'贰',
        '3'=>'叁',
        '4'=>'肆',
        '5'=>'伍',
        '6'=>'陆',
        '7'=>'柒',
        '8'=>'捌',
        '9'=>'玖',
    );

    //循环将数组$arr_left中的值替换成大写
    foreach($arr_left as $k => $v)
    {
        $arr_left[$k] = $daxie[$v];
        switch($len_left--)
        {
            //数值后面追加金额单位
            case 5:
                $arr_left[$k] .= '万';break;
            case 4:
                $arr_left[$k] .= '仟';break;
            case 3:
                $arr_left[$k] .= '佰';break;
            case 2:
                $arr_left[$k] .= '十';break;
            default:
                $arr_left[$k] .= '元';break;
        }
    }
    //print_r($arr_left);
    //output :Array ( [0] => 壹万 [1] => 贰千 [2] => 叁百 [3] => 肆十 [4] => 伍元 )

    foreach($arr_right as $k =>$v)
    {
        $arr_right[$k] = $daxie[$v];
        switch($len_right--)
        {
            case 2:
                $arr_right[$k] .= '角';break;
            default:
                $arr_right[$k] .= '分';break;
        }
    }
    //print_r($arr_right);
    //output :Array ( [0] => 陆角 [1] => 柒分 )

    //将数组转换成字符串，并拼接在一起
    $new_left_str = implode('',$arr_left);
    $new_right_str = implode('',$arr_right);

    $new_str = $new_left_str.$new_right_str;

    //echo $new_str;
    //output :'壹万贰千叁百肆十伍元陆角柒分'

    //如果金额中带有0，大写的字符串中将会带有'零千零百零十',这样的字符串，需要替换掉
    $new_str = str_replace('零万','零',$new_str);
    $new_str = str_replace('零仟','零',$new_str);
    $new_str = str_replace('零佰','零',$new_str);
    $new_str = str_replace('零十','零',$new_str);
    $new_str = str_replace('零零零','零',$new_str);
    $new_str = str_replace('零零','零',$new_str);
    $new_str = str_replace('零元','元',$new_str);


    //echo'<br/>';
    return $new_str;
}

function getTempstr($do){
    if($do=="xuxue"){
        $tempstr = "续学";
    }
    elseif($do=="zhuan")
    {
        $tempstr = "转课";
    }
    else
    {
        $tempstr = "新增";
    }
    return $tempstr;
}

//反序列化图片
function show_img($imgs){
    $datas = unserialize($imgs);
    $tempstr = '';
    foreach($datas as $vo){
        $tempstr = $tempstr."<a href='".$vo."' target='_blank'><img height='40' src='".$vo."'>";
    }
    return $tempstr;
}

//无限极分类
function unlimitedForLevel($arr){
    $refer = array();
    $tree = array();
    foreach($arr as $k => $v){
        $refer[$v['id']] = & $arr[$k]; //创建主键的数组引用
    }
    foreach($arr as $k => $v){
        $parentid = $v['parentid'];  //获取当前分类的父级id
        if($parentid == 0){
            $tree[] = & $arr[$k];  //顶级栏目
        }else{
            if(isset($refer[$parentid])){
                $refer[$parentid]['subcat'][] = & $arr[$k]; //如果存在父级栏目，则添加进父级栏目的子栏目数组中
            }
        }
    }
    return $tree;
}

