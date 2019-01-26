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