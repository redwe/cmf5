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

        $data = Db::name("zhibo_lubo")->where($map)->order('id desc')->paginate(20)->each(function($v,$k){
              $info = DB::name('zhibo_course')->where(['id'=>$v['cid']])->find();
              $v['zhibo_name']=$info['course_name'];
            return $v;
        });
        //dump($data);
        $this->assign('data',$data);
        $this->assign('page',$data->render());
        return $this->fetch();
    }

    public function addlubo(){
        if($this->request->isPost()){
            $course_name = $this->request->param('post.course_name','');
            $teacher_name = $this->request->param('post.teacher_name','');
            $course_cate_level1 = $this->request->param('post.course_cate_level1',0);
            $course_cate_level2 = $this->request->param('post.course_cate_level2',0);
            $kb_time = $this->request->param('post.kb_time','');
            $video_url = $this->request->param('post.video_url','');
            $course_img_old = $this->request->param('post.course_img_old','');
            $cid = $this->request->param('post.cid','');
            if(empty($course_name)){
                $this->error('课堂名称不能为空');
            }
            if(empty($course_cate_level1)){
                $this->error('分类不能为空');
            }
            if(empty($course_cate_level2)){
                $this->error('科目不能为空');
            }
            if(empty($kb_time)){
                $this->error('开播时间不能为空');
            }
            if(empty($video_url)){
                $this->error('视频地址不能为空');
            }
            if(empty($cid)){
                $this->error('所属直播错误');
            }

            $updata = new Upload();// 实例化上传类
            if(request()->file('file')){
                $uploads = $updata->uploadpic('file',"/uploads/zhibo/");
                if ($uploads['res']) {
                    $course_img = $uploads['data'];
                }
            }

            if(empty($course_img)){
                $course_img = $course_img_old;
            }
            $save_data = array(
                'course_name'=>$course_name,
                'create_time'=>time(),
                'see_num'=>0,
                'teacher_name'=>$teacher_name,
                'course_img'=>$course_img,
                'course_cate_level1'=>$course_cate_level1,
                'course_cate_level2'=>$course_cate_level2,
                'kb_time'=>strtotime($kb_time),
                'video_url'=>$video_url,
                'cid'=>$cid
            );
            if(Db::name("zhibo_lubo")->insert($save_data)){
                $this->success('创建成功',url('Zhibo/lubo'));
            }else {
                $this->error('创建失败');
            }
        }else{
            $l1_type_data = Db::name("portal_category")->where(array('parent_id'=>0,'delete_time'=>0))->order('list_order asc')->select();
            $course_data = Db::name("zhibo_course")->select();
            $this->assign('l1_type_data',$l1_type_data);
            $this->assign('course_data',$course_data);
            return $this->fetch();
        }
    }

    //编辑录播
    public function editlubo(){
        $id = $this->request->param('id','');
        if(empty($id)){
            $this->error('参数错误');
        }
        if($this->request->isPost()){
            $course_name = $this->request->param('post.course_name','');
            $teacher_name = $this->request->param('post.teacher_name','');
            $course_img_old = $this->request->param('post.course_img_old','');
            $course_cate_level1 = $this->request->param('post.course_cate_level1',0);
            $course_cate_level2 = $this->request->param('post.course_cate_level2',0);
            $kb_time = $this->request->param('post.kb_time','');
            $video_url = $this->request->param('post.video_url','');
            $cid = $this->request->param('post.cid','');
            if(empty($course_name)){
                $this->error('课堂名称不能为空');
            }
            if(empty($teacher_name)){
                $this->error('主讲老师不能为空');
            }
            if(empty($course_cate_level1)){
                $this->error('分类不能为空');
            }
            if(empty($course_cate_level2)){
                $this->error('科目不能为空');
            }
            if(empty($kb_time)){
                $this->error('开播时间不能为空');
            }
            if(empty($video_url)){
                $this->error('视频地址不能为空');
            }
            if(empty($cid)){
                $this->error('所属直播错误');
            }
            $updata = new Upload();// 实例化上传类
            if(request()->file('file')){
                $uploads = $updata->uploadpic('file',"/uploads/zhibo/");
                if ($uploads['res']) {
                    $course_img = $uploads['data'];
                }
            }
            if(empty($course_img)){
                $course_img = $course_img_old;
            }

            $save_data = array(
                'course_name'=>$course_name,
                'teacher_name'=>$teacher_name,
                'course_img'=>$course_img,
                'course_cate_level1'=>$course_cate_level1,
                'course_cate_level2'=>$course_cate_level2,
                'kb_time'=>strtotime($kb_time),
                'video_url'=>$video_url,
                'cid'=>$cid
            );
            if(Db::name('zhibo_lubo')->where(array("id"=>$id))->update($save_data)){
                $this->success('编辑成功',url('Zhibo/lubo'));
            }else {
                $this->error('编辑失败');
            }
        }else {
            $data = Db::name('zhibo_lubo')->where(array("id"=>$id))->find();
            $l1_type_data = Db::name("portal_category")->where(array('parent_id'=>0,'delete_time'=>0))->order('list_order asc')->select();
            $l2_type_data = Db::name("portal_category")->where(array('parent_id'=>$data['course_cate_level1'],'delete_time'=>0))->order('list_order asc')->select();
            $course_data = Db::name("zhibo_course")->select();
            $this->assign('l1_type_data',$l1_type_data);
            $this->assign('l2_type_data',$l2_type_data);
            $this->assign('course_data',$course_data);
            $this->assign('data',$data);
            return $this->fetch();
        }
    }

    //删除录播
    public function dellubo(){
        $id = $this->request->param('id','');
        if(empty($id)){
            $this->error('参数错误');
        }
        $data = Db::name('zhibo_lubo')->where(array("id"=>$id))->find();
        if(empty($data)){
            $this->error('已删除，无法重复删除');
        }

        if(Db::name('zhibo_lubo')->where(array("id"=>$id))->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function yuyue(){
        $join = [
          ["member m","m.id=y.uid"],
          ["zhibo_course c","c.id=y.cid","left"]
        ];
        $field = 'y.*,m.username,c.course_name';
        $data = Db::name("zhibo_yuyue")->alias("y")->join($join)->field($field)->paginate(20);
        //dump($data);
        $this->assign('data',$data);
        $this->assign('page',$data->render());
        return $this->fetch();
    }

    public function zhibotype(){
        // 分类级数 1时间 2项目 3科目 4视频类型 5班次
        $data1 = Db::name('zhibo_cate')->where(['parentid'=>0])->select();
        $html = '';
        if($data1){
            foreach ($data1 as $key1 => $val1) {
                $where['typestr']=['like',"%$val1[id]%"];
                $count1 = Db::name('post_lession')->where($where)->count();
                $html .= '<tr data-tt-id="'.$val1['id'].'"><td>' . $val1['classname']. '</td><td>' . $val1['id'] . '</td><td><a href="'.url('Zhibo/addtype',
                        array('id'=>$val1['id'])).'"">添加子分类</a>  <a href="'.url('Zhibo/edittype',array('id'=>$val1['id'])).'">编辑</a>  <a  href="'.url('Zhibo/deltype',array('id'=>$val1['id'])).'">删除</a></td></tr>';
                $data2 = Db::name('zhibo_cate')->where(['parentid' => $val1['id']])->select();
                if($data2){
                    foreach ($data2 as $key2=>$val2){
                        $where['typestr']=['like',"%$val2[id]%"];
                        $count2 = Db::name('post_lession')->where($where)->count();
                        $html .= '<tr data-tt-id="'.$val2['id'].'" data-tt-parent-id="'.$val2['parentid'].'"><td>' . $val2['classname'] .  '</td><td>' .
                            $val2['id'] . '</td><td><a href="'.url('Zhibo/edittype',array('id'=>$val2['id'])).'">编辑</a>  <a  href="'.url('Zhibo/deltype',array('id'=>$val2['id'])).'">删除</a> </td></tr>';
                    }
                }

            }
        }
        $this->assign('html',$html);
        return $this->fetch();
    }

    public function addtype(){

        $id = $this->request->param('id');
        if($id == 0){
            $data['id'] = 0;
            $data['parentstr'] = '';
            $data['classname'] = '顶级分类';
        }else {
            $data = Db::name('zhibo_cate')->where(['id'=>$id])->find();
        }
        if(empty($data)){
            $this->error('上级分类错误');
        }
        //提交分类
        if($this->request->isPost()){
            $classname = $this->request->param('classname');
            $sort = $this->request->param('sort');
            if(empty($classname)){
                $this->error('分类名称不能为空');
            }
            $save_data = array(
                'parentid'=>$id,
                'parentstr'=>$data['parentstr'].$id.',',
                'classname'=>$classname,
                'sort'=>$sort,
            );
            if(Db::name('zhibo_cate')->insert($save_data)){
                $this->success('添加成功',url('zhibo/zhibotype'));
            }else{
                $this->error('添加失败');
            }
            exit();
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    //编辑分类
    public function edittype(){
        $id = $this->request->param('id');
        if(empty($id)){
            $this->error('参数错误');
        }
        $data = Db::name('zhibo_cate')->where(['id'=>$id])->find();
        if(empty($data)){
            $this->error('分类错误');
        }
        //提交分类
        if($this->request->isPost()){
            $classname = $this->request->param('classname');
            $sort = $this->request->param('sort');

            if(empty($classname)){
                $this->error('分类名称不能为空');
            }
            $save_data = array(
                'classname'=>$classname,
                'sort'=>$sort,
                'id'=>$id
            );
            if(Db::name('zhibo_cate')->update($save_data)){
                $this->success('编辑成功',url('zhibo/zhibotype'));
            }else{
                $this->error('编辑失败');
            }
            exit();
        }
        $this->assign('data',$data);
        return $this->fetch();
    }
    //删除分类
    public function deltype(){
        $id = $this->request->param('id');
        if(empty($id)){
            $this->error('参数错误');
        }
        $data = Db::name('zhibo_cate')->where(array("id"=>$id))->find();
        if(empty($data)){
            $this->error('分类错误');
        }
        //查询分类下面是否还有子分类
        $fdata = Db::name('zhibo_cate')->where(array('parentid'=>$id))->find();
        if($fdata){
            $this->error('该分类下存在子分类！请先删除子分类后重试');
        }else{
            if(Db::name('zhibo_cate')->where(array("id"=>$id))->delete()){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }
}
