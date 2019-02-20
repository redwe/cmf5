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
use app\admin\model\Upload;

class ZhiboController extends AdminBaseController
{

    public function index(){

       $course_name = $this->request->param('course_name','');
        $type = $this->request->param('type','');
        $map = array();
        if(!empty($course_name)){
            $map['course_name'] = array('LIKE','%'.$course_name.'%');
        }
        if(!empty($type)){
            $map['type'] = $type;
        }
        $this->assign('course_name',$course_name);
        $this->assign('type',$type);
        $type_data = Db::name("portal_category")->where(array('delete_time'=>0))->select();
        $this->assign('type_data',arrayValueToKey($type_data));

        $data = Db::name("zhibo_course")->where($map)->order('id desc')->paginate(20)->each(function($v,$k){
            $result = explode(',',$v['live_cate']);
            $arr = Db::name('zhibo_cate')->where(['id'=>$result[1]])->find();
            if($arr['parentid']>0){
                $info = DB::name('zhibo_cate')->where(['id'=>$arr['parentid']])->find();
                $cate = $info['classname'].'->'.$arr['classname'];
            }else{
                $cate = $arr['classname'];
            }
            $v['live_cate']=$cate;
            return $v;
        });
        //dump($data);
        $this->assign('data',$data);
        $this->assign('page',$data->render());
        return $this->fetch();
    }

    public function addzhibo(){

        if($this->request->isPost()){
            $updata = new Upload();// 实例化上传类
            if(request()->file('file')){
                $uploads = $updata->uploadpic('file',"/uploads/zhibo/");
                if ($uploads['res']) {
                    $datas['course_img'] = $uploads['data'];
                }
            }
            //$stuArr = explode(',',$_POST['stu_num']);
            $stuArr = $_POST['post'];
            $title=[];
            $map['p.category_id'] = ['in',$stuArr['categories']];
            $join = [
                ["portal_category_post p","p.post_id=g.id"],
                ["portal_category c","c.id=p.category_id"],
                ["carts t","t.goods_id=g.id"],
                ["member m","m.id=t.member_id"]
            ];
            $field = 'g.post_title,m.username';
            $titles = Db::name('portal_post')->alias("g")->join($join)->field($field)->where($map)->select();
            $stu=[];
            $str=[];
            foreach ($titles as $key=>$value){
                foreach($value as $c){
                    $stu[] = $c['post_title'];
                    $str[] = $c['username'];
                }
            }
            $str=array_unique($str);
            $datas['see_num'] = count($str);
            $str = implode(',',$str);

              $datas['students'] = $str;
              $datas['create_time']=time();
              $datas["course_name"] = $_POST["course_name"];
              $datas["live_room"] = $_POST["live_room"];
              $datas["live_time"] = $_POST["live_time"];
              $datas["kb_time"] = $_POST["kb_time"];
              $datas["live_cate"] = $_POST["live_cate"];
              $datas["teacher_name"] = $_POST["teacher_name"];
              $datas["teacher_token"] = $_POST["teacher_token"];
              $datas["assistant_token"] = $_POST["assistant_token"];
              $datas["student_token"] = $_POST["student_token"];
              $datas["sdk_id"] = $_POST["sdk_id"];
              $datas["orderby"] = $_POST["orderby"];
              $datas["type"] = $_POST["type"];
              //$datas["categories"] = $_POST["post"]["categories"];
              $datas["students"] = $_POST["other_stu"];
              $datas["content"] = $_POST["content"];

            if(Db::name("zhibo_course")->insert($datas)){
                $this->success('创建成功',url('Zhibo/index'));
            }else {
                $this->error('创建失败');
            }
        }
        $arr=Db::name('zhibo_cate')->select();
        $arr = json_decode($arr,true);
        $live_cate = unlimitedForLevel($arr);
        $this->assign('live_cate',$live_cate);
        return $this->fetch();
    }

    public function editzhibo(){
        if($this->request->isPost()){
            $id = $this->request->param('id','');
            $updata = new Upload();// 实例化上传类
            if(request()->file('file')){
                $uploads = $updata->uploadpic('file',"/uploads/zhibo/");
                if ($uploads['res']) {
                    $datas['course_img'] = $uploads['data'];
                }
            }
            $datas['update_time']=time();
            $stuArr = $_POST['post'];
            $title=[];
            $map['p.category_id'] = ['in',$stuArr['categories']];
            $join = [
                ["portal_category_post p","p.post_id=g.id"],
                ["portal_category c","c.id=p.category_id"],
                ["carts t","t.goods_id=g.id"],
                ["member m","m.id=t.member_id"]
            ];
            $field = 'g.post_title,m.username';
            $titles = Db::name('portal_post')->alias("g")->join($join)->field($field)->where($map)->select();
            $stu=[];
            $str=[];
            foreach ($titles as $key=>$value){
                foreach($value as $c){
                    $stu[] = $c['post_title'];
                    $str[] = $c['username'];
                }
            }
            $str=array_unique($str);
            $datas['see_num'] = count($str);
            $str = implode(',',$str);

            $datas['students'] = $str;
            $datas['create_time']=time();
            $datas["course_name"] = $_POST["course_name"];
            $datas["live_room"] = $_POST["live_room"];
            $datas["live_time"] = $_POST["live_time"];
            $datas["kb_time"] = $_POST["kb_time"];
            $datas["live_cate"] = $_POST["live_cate"];
            $datas["teacher_name"] = $_POST["teacher_name"];
            $datas["teacher_token"] = $_POST["teacher_token"];
            $datas["assistant_token"] = $_POST["assistant_token"];
            $datas["student_token"] = $_POST["student_token"];
            $datas["sdk_id"] = $_POST["sdk_id"];
            $datas["orderby"] = $_POST["orderby"];
            $datas["type"] = $_POST["type"];
            //$datas["categories"] = $_POST["post"]["categories"];
            $datas["students"] = $_POST["other_stu"];
            $datas["content"] = $_POST["content"];

            if(Db::name('zhibo_cate')->where(array("id"=>$id))->update($datas)){
                $this->success('编辑成功',url('Zhibo/index'));
            }else {
                $this->error('编辑失败');
            }
        }else {
            $id = $this->request->param('id','');
            if(empty($id)){
                $this->error('参数错误');
            }
            $data = Db::name('zhibo_course')->where(array("id"=>$id))->find();
            $this->assign('data',$data);

            $arr=Db::name('zhibo_cate')->select();
            $arr = json_decode($arr,true);
            $live_cate = unlimitedForLevel($arr);

            $this->assign('live_cate',$live_cate);
            return $this->fetch();
        }
    }

    public function delzhibo(){
        $id = $this->request->param('id','');
        if(empty($id)){
            $this->error('参数错误');
        }
        if(Db::name("zhibo_course")->where(array("id"=>$id))->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function lubo(){
        $course_name = $this->request->param('course_name','');
        $type = $this->request->param('type','');
        $map = array();
        if(!empty($course_name)){
            $map['course_name'] = array('LIKE','%'.$course_name.'%');
        }
        if(!empty($type)){
            $map['type'] = $type;
        }
        $this->assign('course_name',$course_name);
        $this->assign('type',$type);
        $type_data = Db::name("portal_category")->where(array('delete_time'=>0))->select();
        $this->assign('type_data',arrayValueToKey($type_data));

        $data = Db::name("zhibo_course")->where($map)->order('id desc')->paginate(20)->each(function($v,$k){
            $result = explode(',',$v['live_cate']);
            $arr = Db::name('zhibo_cate')->where(['id'=>$result[1]])->find();
            if($arr['parentid']>0){
                $info = DB::name('zhibo_cate')->where(['id'=>$arr['parentid']])->find();
                $cate = $info['classname'].'->'.$arr['classname'];
            }else{
                $cate = $arr['classname'];
            }
            $v['live_cate']=$cate;
            return $v;
        });
        //dump($data);
        $this->assign('data',$data);
        $this->assign('page',$data->render());
        return $this->fetch();
    }

    public function addlubo(){
        if($this->request->isPost()){
            $updata = new Upload();// 实例化上传类
            if(request()->file('file')){
                $uploads = $updata->uploadpic('file',"/uploads/zhibo/");
                if ($uploads['res']) {
                    $datas['course_img'] = $uploads['data'];
                }
            }
            //$stuArr = explode(',',$_POST['stu_num']);
            $stuArr = $_POST['post'];
            $title=[];
            $map['p.category_id'] = ['in',$stuArr['categories']];
            $join = [
                ["portal_category_post p","p.post_id=g.id"],
                ["portal_category c","c.id=p.category_id"],
                ["carts t","t.goods_id=g.id"],
                ["member m","m.id=t.member_id"]
            ];
            $field = 'g.post_title,m.username';
            $titles = Db::name('portal_post')->alias("g")->join($join)->field($field)->where($map)->select();
            $stu=[];
            $str=[];
            foreach ($titles as $key=>$value){
                foreach($value as $c){
                    $stu[] = $c['post_title'];
                    $str[] = $c['username'];
                }
            }
            $str=array_unique($str);
            $datas['see_num'] = count($str);
            $str = implode(',',$str);

            $datas['students'] = $str;
            $datas['create_time']=time();
            $datas["course_name"] = $_POST["course_name"];
            $datas["live_room"] = $_POST["live_room"];
            $datas["live_time"] = $_POST["live_time"];
            $datas["kb_time"] = $_POST["kb_time"];
            $datas["live_cate"] = $_POST["live_cate"];
            $datas["teacher_name"] = $_POST["teacher_name"];
            $datas["teacher_token"] = $_POST["teacher_token"];
            $datas["assistant_token"] = $_POST["assistant_token"];
            $datas["student_token"] = $_POST["student_token"];
            $datas["sdk_id"] = $_POST["sdk_id"];
            $datas["orderby"] = $_POST["orderby"];
            $datas["type"] = $_POST["type"];
            //$datas["categories"] = $_POST["post"]["categories"];
            $datas["students"] = $_POST["other_stu"];
            $datas["content"] = $_POST["content"];

            if(Db::name("zhibo_course")->insert($datas)){
                $this->success('创建成功',url('Zhibo/index'));
            }else {
                $this->error('创建失败');
            }
        }
        $arr=Db::name('zhibo_cate')->select();
        $arr = json_decode($arr,true);
        $live_cate = unlimitedForLevel($arr);
        $this->assign('live_cate',$live_cate);
        return $this->fetch();
    }

    public function yuyue(){
        $course_name = $this->request->param('course_name','');
        $type = $this->request->param('type','');
        $map = array();
        if(!empty($course_name)){
            $map['course_name'] = array('LIKE','%'.$course_name.'%');
        }
        if(!empty($type)){
            $map['type'] = $type;
        }
        $this->assign('course_name',$course_name);
        $this->assign('type',$type);
        $type_data = Db::name("portal_category")->where(array('delete_time'=>0))->select();
        $this->assign('type_data',arrayValueToKey($type_data));

        $data = Db::name("zhibo_course")->where($map)->order('id desc')->paginate(20)->each(function($v,$k){
            $result = explode(',',$v['live_cate']);
            $arr = Db::name('zhibo_cate')->where(['id'=>$result[1]])->find();
            if($arr['parentid']>0){
                $info = DB::name('zhibo_cate')->where(['id'=>$arr['parentid']])->find();
                $cate = $info['classname'].'->'.$arr['classname'];
            }else{
                $cate = $arr['classname'];
            }
            $v['live_cate']=$cate;
            return $v;
        });
        //dump($data);
        $this->assign('data',$data);
        $this->assign('page',$data->render());
        return $this->fetch();
    }

    public function zhibotype(){
        $course_name = $this->request->param('course_name','');
        $type = $this->request->param('type','');
        $map = array();
        if(!empty($course_name)){
            $map['course_name'] = array('LIKE','%'.$course_name.'%');
        }
        if(!empty($type)){
            $map['type'] = $type;
        }
        $this->assign('course_name',$course_name);
        $this->assign('type',$type);
        $type_data = Db::name("portal_category")->where(array('delete_time'=>0))->select();
        $this->assign('type_data',arrayValueToKey($type_data));

        $data = Db::name("zhibo_course")->where($map)->order('id desc')->paginate(20)->each(function($v,$k){
            $result = explode(',',$v['live_cate']);
            $arr = Db::name('zhibo_cate')->where(['id'=>$result[1]])->find();
            if($arr['parentid']>0){
                $info = DB::name('zhibo_cate')->where(['id'=>$arr['parentid']])->find();
                $cate = $info['classname'].'->'.$arr['classname'];
            }else{
                $cate = $arr['classname'];
            }
            $v['live_cate']=$cate;
            return $v;
        });
        //dump($data);
        $this->assign('data',$data);
        $this->assign('page',$data->render());
        return $this->fetch();
    }

    public function addtype(){
        if($this->request->isPost()){
            $updata = new Upload();// 实例化上传类
            if(request()->file('file')){
                $uploads = $updata->uploadpic('file',"/uploads/zhibo/");
                if ($uploads['res']) {
                    $datas['course_img'] = $uploads['data'];
                }
            }
            //$stuArr = explode(',',$_POST['stu_num']);
            $stuArr = $_POST['post'];
            $title=[];
            $map['p.category_id'] = ['in',$stuArr['categories']];
            $join = [
                ["portal_category_post p","p.post_id=g.id"],
                ["portal_category c","c.id=p.category_id"],
                ["carts t","t.goods_id=g.id"],
                ["member m","m.id=t.member_id"]
            ];
            $field = 'g.post_title,m.username';
            $titles = Db::name('portal_post')->alias("g")->join($join)->field($field)->where($map)->select();
            $stu=[];
            $str=[];
            foreach ($titles as $key=>$value){
                foreach($value as $c){
                    $stu[] = $c['post_title'];
                    $str[] = $c['username'];
                }
            }
            $str=array_unique($str);
            $datas['see_num'] = count($str);
            $str = implode(',',$str);

            $datas['students'] = $str;
            $datas['create_time']=time();
            $datas["course_name"] = $_POST["course_name"];
            $datas["live_room"] = $_POST["live_room"];
            $datas["live_time"] = $_POST["live_time"];
            $datas["kb_time"] = $_POST["kb_time"];
            $datas["live_cate"] = $_POST["live_cate"];
            $datas["teacher_name"] = $_POST["teacher_name"];
            $datas["teacher_token"] = $_POST["teacher_token"];
            $datas["assistant_token"] = $_POST["assistant_token"];
            $datas["student_token"] = $_POST["student_token"];
            $datas["sdk_id"] = $_POST["sdk_id"];
            $datas["orderby"] = $_POST["orderby"];
            $datas["type"] = $_POST["type"];
            //$datas["categories"] = $_POST["post"]["categories"];
            $datas["students"] = $_POST["other_stu"];
            $datas["content"] = $_POST["content"];

            if(Db::name("zhibo_course")->insert($datas)){
                $this->success('创建成功',url('Zhibo/index'));
            }else {
                $this->error('创建失败');
            }
        }
        $arr=Db::name('zhibo_cate')->select();
        $arr = json_decode($arr,true);
        $live_cate = unlimitedForLevel($arr);
        $this->assign('live_cate',$live_cate);
        return $this->fetch();
    }
}
