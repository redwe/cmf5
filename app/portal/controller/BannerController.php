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

class BannerController extends AdminBaseController
{

    public function index(){

        $data = Db::name("banner")->order('sort asc')->paginate(20);
        $this->assign('data',$data);
        $this->assign('page',$data->render());
        return $this->fetch();
    }

    public function addbanner(){
        if($this->request->isPost()){
            $updata = new Upload();// 实例化上传类
            if(request()->file('file')){
                $uploads = $updata->uploadpic('file');
                if ($uploads['res']) {
                    $_POST['pic'] = $uploads['data'];
                }
            }
            $_POST['createtime'] = time();
            $info = Db::name('banner')->insert($_POST);
            if($info){
                $this->success('成功','index');
            }else{
                $this->error('失败');
            }
        }
        return $this->fetch();
    }

    //修改排序
    public function editSort(){
        $data['id'] = $_POST['id'];
        $data['sort'] = $_POST['sort'];
        $info = Db::name('banner')->update($data);
        if($info>0){
            echo 1;
        }else{
            echo 0;
        }
    }

    public function glist(){
        $list = Db::name('guanggao')->select();
        if($this->request->isPost()){
            if($_FILES['file']['error']>0){
                $data['pic'] = $_POST['pic'];
            }else{
                $data['pic'] = $this->uploadPic();
            }
            $data['id'] = $_POST['id'];
            $data['updatetime'] = time();
            $info = Db::name('guanggao')->update($data);
            if($info){
                $this->success('编辑成功',url('Banner/glist'));
            }else{
                $this->error('编辑失败',url('Banner/glist'));
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function editbanner(){
        $id = $this->request->param('id');
        if($this->request->isPost()){
            if($_FILES['file']['error']>0){
                $data['pic']=$_POST['pic'];
            }else{
                $data['pic'] = $this->uploadPic();
            }
            $data['updatetime']=time();
            $data['name']=$_POST['name'];
            $data['url']=$_POST['url'];
            $data['sort']=$_POST['sort'];
            $info = Db::name('banner')->where(array("id"=>$id))->update($data);
            if($info){
                $this->success('编辑成功',url('Banner/index'));
            }else{
                $this->error('编辑失败',url('Banner/index'));
            }
        }
        $info = Db::name('banner')->where(['id'=>$id])->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    //删除Banner
    public function delBanner(){
        $id=$_GET['id'];
        $result = Db::name('banner')->where(['id'=>$id])->delete();
        if($result){
            $this->success('删除成功',url('Banner/index'));
        }else{
            $this->error('删除成功',url('Banner/index'));
        }
    }

    //首页员工风采
    public function fengcai(){
        $list = Db::name('fengcai')->select();
        if($this->request->isPost()){
            if($_FILES['file']['error']>0){
                $data['pic']=$_POST['pic'];
            }else{
                $data['pic'] = $this->uploadPic();
            }
            $where['id']=$_POST['id'];
            $data['updatetime']=time();
            $data['url']=$_POST['url'];
            $info = Db::name('fengcai')->where($where)->update($data);
            if($info){
                $this->success('编辑成功',url('Banner/fengcai'));
            }else{
                $this->error('编辑失败',url('Banner/fengcai'));
            }
        }
        dump($list);
        $this->assign('list',$list);
        return $this->fetch();
    }
    //首页合作企业
    public function qiye(){
        $list = Db::name('qiye')->select();
        if($this->request->isPost()){
            if($_FILES['file']['error']>0){
                $data['pic']=$_POST['pic'];
            }else{
                $data['pic'] = $this->uploadPic();
            }
            $where['id']=$_POST['id'];
            $data['updatetime']=time();
            $data['url']=$_POST['url'];
            $info = Db::name('qiye')->where($where)->update($data);
            if($info){
                $this->success('编辑成功',url('Banner/Banner/qiye'));
            }else{
                $this->error('编辑失败',url('Banner/Banner/qiye'));
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function uploadPic(){
        $address = "";
        $updata = new Upload();// 实例化上传类
        if(request()->file('file')){
            $uploads = $updata->uploadpic('file');
            if ($uploads['res']) {
                $address = $uploads['data'];
            }
        }
        return $address;
    }
}
