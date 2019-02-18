<?php
namespace app\admin\model;

use think\Model;
use think\DB;
use think\Session;

class Upload extends Model
{
    //获取省份、分部、身份、岗位
    public function uploadpic($filename,$url='',$array=''){
        $rootUrl = $_SERVER['DOCUMENT_ROOT'];
        $file = $_FILES[$filename];

        if(empty($url)){
            $url = '/upload/images/';
        }
        if(empty($array)) {
            $array = ["jpg", "gif", "png"];
        }

        if (!empty($file['name'])) {
            $fileName = $file['name'];    //得到文件全名
            $dotArray = explode('.', $fileName);    //把文件名安.区分，拆分成数组
            $type = end($dotArray);
            $type = strtolower($type);

            if (!in_array($type,$array)) {
                $ret['res'] = "0";
                $ret['msg'] = "格式不正确，请重新上传!";
                $ret['data'] = '';
                $ret['fileurl'] = '';
                //return json_encode($ret);
                return $ret;
            }

            //取数组最后一个元素，得到文件类型
            $uploaddir1 = date("Ymd") . '/';//设置文件保存目录 注意包含
            $uploaddir0 = $url . $uploaddir1;//设置文件保存目录 注意包含
            if (!file_exists($rootUrl.$uploaddir0)) {
                mkdir($rootUrl.$uploaddir0, 0777, true);
            }
            $savename = md5(uniqid(rand())) . '.' . $type;
            $uploaddir = $uploaddir0. $savename;
            $fileurl = $uploaddir1. $savename;
            $path = $rootUrl.$uploaddir; //产生随机文件名
            //$path = "images/".$fileName; //客户端上传的文件名；
            //下面必须是tmp_name 因为是从临时文件夹中移动
            //sleep(1);
            //dump($file['tmp_name']);
            if($file['tmp_name']){
                move_uploaded_file($file['tmp_name'], $path); //从服务器临时文件拷贝到相应的文件夹下
            }
            else
            {
                $ret['res'] = "0";
                $ret['msg'] = "上传文件丢失!" . $file['error'];
                $ret['data'] = '';
                $ret['fileurl'] = '';
            }
            //$file_path = $path;

            if($type=='jpg' || $type=="jpeg" || $type=="png"){
                $image = \think\Image::open($path);
                $portrait_thumbnail_180 = $rootUrl.$uploaddir0."thumb_".$savename;
                //dump($portrait_thumbnail_180);
                $image->thumb(180, 180, \think\Image::THUMB_CENTER)->save($portrait_thumbnail_180, null, 100, true);
            }

            if (!file_exists($path)) {
                $ret['res'] = "0";
                $ret['msg'] = "上传文件丢失!" . $file['error'];
                $ret['data'] = '';
                $ret['fileurl'] = '';
            }
            else
            {
                $ret['res'] = "1";
                $ret['msg'] = "上传文件成功!";
                $ret['data'] = $uploaddir;
                $ret['fileurl'] = $fileurl;
            }
            return $ret;
        }
    }

    public function thumbImage($file,$pic,$uploaddir,$filename)      //$file保存文件路径，$pic上传文件路径,$uploaddir保存目录
    {
        $image = \think\Image::open($file);
        $getSaveName = str_replace('\\', '/', $pic->getSaveName());

        $portrait_thumbnail_180 = $uploaddir . str_replace($pic->getFilename(), '180_' . $pic->getFilename(), $getSaveName);
        $image = \think\Image::open($file);
        $image->thumb(180, 180, \think\Image::THUMB_CENTER)->save(ROOT_PATH . DS . $portrait_thumbnail_180, null, 100, true);
        /*
        $portrait_thumbnail_80 = $uploaddir . str_replace($pic->getFilename(), '80_' . $pic->getFilename(), $getSaveName);
        $image->thumb(80, 80, \think\Image::THUMB_CENTER)->save(ROOT_PATH . DS . $portrait_thumbnail_80, null, 100, true);
        $portrait_thumbnail_50 = $uploaddir . str_replace($pic->getFilename(), '50_' . $pic->getFilename(), $getSaveName);
        $image->thumb(50, 50, \think\Image::THUMB_CENTER)->save(ROOT_PATH . DS . $portrait_thumbnail_50, null, 100, true);
        */

        if ($image) {
            return $getSaveName;
        }
    }

    public function importExcels($path,$zytype,$uid=0)
    {

        $zyObject = new ZyModel();
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

        $province = Session::get("province");

        for ($j = 2; $j <= $highestRow; $j++) {
            $importRows++;

            $datas['guest'] = (string)$objPHPExcel->getActiveSheet()->getCell("A$j")->getValue();   //需要导入的客户名称
            $datas['tel'] = (string)$objPHPExcel->getActiveSheet()->getCell("B$j")->getValue();   //需要导入的客户手机
            $datas['company'] = (string)$objPHPExcel->getActiveSheet()->getCell("C$j")->getValue();   //需要导入的客户单位
            $datas['project'] = (string)$objPHPExcel->getActiveSheet()->getCell("D$j")->getValue();//需要导入的项目名
            //$datas['profes'] = (string)$objPHPExcel->getActiveSheet()->getCell("E$j")->getValue();//需要导入的专业
            //$datas['class'] = (string)$objPHPExcel->getActiveSheet()->getCell("F$j")->getValue();//需要导入的科目
            //$datas['email'] = (string)$objPHPExcel->getActiveSheet()->getCell("G$j")->getValue();//需要导入的邮箱
            $shengfen = (string)$objPHPExcel->getActiveSheet()->getCell("E$j")->getValue();
            $datas['province'] = $shengfen;   //需要导入的省份
            $datas['city'] = (string)$objPHPExcel->getActiveSheet()->getCell("F$j")->getValue(); //需要导入的城市
            //$create = (string)$objPHPExcel->getActiveSheet()->getCell("J$j")->getValue();     //需要导入的时间
            //$datas['create'] = $zyObject->getTimeFormat($create);
            $datas['intent'] = (string)$objPHPExcel->getActiveSheet()->getCell("G$j")->getValue();   //需要导入的意向
            $datas['label'] = (string)$objPHPExcel->getActiveSheet()->getCell("H$j")->getValue();   //需要导入的标签
            $datas['marks'] = (string)$objPHPExcel->getActiveSheet()->getCell("I$j")->getValue();   //需要导入的备注
            $datas['datetime'] = date("Y-m-d h:i:s",time());
            $datas['status'] = 1;
            $datas['zytype'] = $zytype;
            if (!empty($uid) && $province == $shengfen){
                $datas['uid'] = $uid;
            }
            else
            {
                $datas['uid'] = 0;
            }

            if(!empty($datas['project'])){
                $ret['mdata'] = $zyObject->addZiyuan($datas);   //保存到mysql数据库
                if ($ret['mdata'] && !is_Bool($ret['mdata'])) {
                    $ar[$i] = $ret['mdata'];
                    $i++;
                }
            }
        }
        if ($i > 0) {
            $ret['res'] = "0";
            $ret['errNum'] = $i;
            $ret['allNum'] = $importRows;
            $ret['sucNum'] = $importRows - $i;
            $ret['mdata'] = $ar;
            $ret['msg'] = "导入完毕!";
        }
        $ret['res'] = "1";
        $ret['allNum'] = $importRows;
        $ret['errNum'] = 0;
        $ret['sucNum'] = $importRows;
        $ret['mdata'] = $ar;
        $ret['msg'] = "导入成功!";
        return $ret;
    }

    //发送短信
    function sendMsg($phone,$content){
        //$verify = rand(100000,999999);        //短信验证码
        $flag = 0;
        $params='';//要post的数据
        $argv = array(
            'name'=>'acjy',     //必填参数。用户账号
            'pwd'=>'2D676E307B841FD3D22ECF045670',     //必填参数。（web平台：基本资料中的接口密码）
            //'content'=>'短信验证码为：'.$verify.'，请勿将验证码提供给他人。',   //必填参数。发送内容（1-500 个汉字）UTF-8编码
            //'content'=>$msg,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
            'content'=> $content,   //'短信验证码为：'.$verify.'，请勿将验证码提供给他人。',   //必填参数。发送内容（1-500 个汉字）UTF-8编码
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
            return 1;
        }
        else{
            return false;
        }
    }

}