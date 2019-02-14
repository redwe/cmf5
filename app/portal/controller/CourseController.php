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

class CourseController extends AdminBaseController
{

    public function index(){
        //$id = $this->request->param("id");
        $where["c.status"] = 1;
        $join = [
            ["course_cate s","c.cate_id=s.id"]
        ];
        $field = 'c.*,s.name as cate_name';
        $Course_list = Db::name('Course')->alias("c")->join($join)->field($field)->where($where)->paginate(20);
        $this->assign('Course_list',$Course_list);
        $this->assign('page',$Course_list->render());
        return $this->fetch();
    }

    public function add_audition(){

        if($this->request->isPost()){
            $data   = $_POST;
            $updata = new Upload();// 实例化上传类
            if(request()->file('course_img')){
                $uploads = $updata->uploadpic('course_img');
                if ($uploads['res']) {
                    $data['course_img'] = $uploads['data'];
                }
            }
            $data['status'] = 1;
            $data['add_time'] = time();
            if(Db::name('Course')->insert($data)){
                $this->success('添加成功');
            }else{
                $this->success('添加失败');
            }
        }else{
            $CateInfo = Db::name('CourseCate')->select();
            $this->assign('Cate',$CateInfo);
            return $this->fetch();
        }
    }


    public function edit_audition()
    {
        if($this->request->isPost()){
            $id= $this->request->param('id');
            $data   	= $_POST;
            $updata = new Upload();// 实例化上传类
            if(isset($fileInfo['course_img']['savename'])){
                $uploads = $updata->uploadpic('course_img');
                if ($uploads['res']) {
                    $data['course_img'] = $uploads['data'];
                }
            }else{
                unset($data['course_img']);
            }
            unset($data['id']);
            if(Db::name('Course')->where(array("id"=>$id))->update($data)){
                $this->success('编辑成功',url('Course/index'));
            }else{
                $this->success('编辑失败');
            }
        }else{
            $id = $this->request->param("id");
            $CateInfo = Db::name('CourseCate')->select();
            $courseInfo = Db::name('Course')->where(array("id"=>$id))->find();
            $this->assign('courseInfo',$courseInfo);
            $this->assign('Cate',$CateInfo);
            return $this->fetch();
        }

    }

    public function look_audition()
    {
        $id= $this->request->param('id');
        $join = [
            ["course_cate s","c.cate_id=s.id"]
        ];
        $field = 'c.*,s.name as cate_name';
        $courseInfo = Db::name('Course')->alias("c")->join($join)->field($field)->where(array("c.id"=>$id))->find();
        $this->assign('courseInfo',$courseInfo);
        return $this->fetch();
    }

    public function del_audition()
    {
        $id= $this->request->param('id');
        $data['status']       = '-1';
        if(Db::name('Course')->where(array("id"=>$id))->update($data)){
            $this->success('删除成功');
        }else{
            $this->success('删除失败');
        }
    }
}
