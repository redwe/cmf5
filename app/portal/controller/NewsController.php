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

class NewsController extends AdminBaseController
{

    public function index(){

        $id = $this->request->param("id");
        $where["n.status"] = 0;
        if(!empty($id)){
            $where["n.type"] = $id;
        }
        $join = [
          ["newstype t","n.type=t.id"],
          ["news_class c","n.class=c.id"],
          ["news_xiangmu x","n.xiangmu=x.id"]
        ];
        $field = 'n.id,n.name,n.type,n.class,n.xiangmu,n.recommended,n.sort,n.createtime,t.typename,c.name as cname,x.xname';
        $dataList = Db::name("news")->alias("n")->join($join)->field($field)->order('n.id desc')->paginate(20);
        //dump($dataList);
        $this->assign('newslist',$dataList);
        $this->assign('page',$dataList->render());
        return $this->fetch();
    }

    public function addnews(){

        $type = Db::name('newstype')->select();
        $newsClass = Db::name('news_class')->select();
        $newsXiangmu = Db::name('news_xiangmu')->select();
        if($this->request->isPost()){
            $updata = new Upload();// 实例化上传类
            $file_name = '';
            //$rootUrl = $_SERVER['DOCUMENT_ROOT'];
            $file_path = '/uploads/newspic/';
            $file_array = ['jpg','jpeg','gif','png'];

            if(request()->file('file')){
                $uploads = $updata->uploadpic('file',$file_path,$file_array);
                if ($uploads['res']) {
                    $file_name = $uploads['data'];
                }
            }
            $_POST['pic']= $file_name;
            $_POST['createtime']=time();
            $_POST['introduce']=html_entity_decode($_POST['introduce']);
            $_POST['content']=html_entity_decode($_POST['content']);
            $_POST['class']=html_entity_decode($_POST['newsclass']);
            $_POST['xiangmu']=html_entity_decode($_POST['newsxiangmu']);
            $info= Db::name('news')->insert($_POST);
            if($info){
                $this->success('新闻添加成功');
            }
        }
        $this->assign(
            [
                'type'=>$type,
                'newsClass'=>$newsClass,
                'newsXiangmu'=>$newsXiangmu,
            ]
        );

        return $this->fetch();
    }

    //编辑新闻
    public function editNews(){
        $id=$this->request->param('id');
        if($this->request->isPost()){
            if($_FILES['file']>0){
                $data['pic']=$this->request->param('pic');
            }else{
                $updata = new Upload();// 实例化上传类
                //$rootUrl = $_SERVER['DOCUMENT_ROOT'];
                $file_path = '/uploads/newspic/';
                $file_array = ['jpg','jpeg','gif','png'];

                if(request()->file('file')){
                    $uploads = $updata->uploadpic('file',$file_path,$file_array);
                    if ($uploads['res']) {
                        $data['pic'] = $uploads['data'];
                    }
                }
            }
            $id = $this->request->param('id');
            $data['updatetime']=time();
            $data['introduce']=html_entity_decode($_POST['introduce']);
            $data['content']=html_entity_decode($_POST['content']);
            $data['class']=html_entity_decode($_POST['newsclass']);
            $data['xiangmu']=html_entity_decode($_POST['newsxiangmu']);
            $info = Db::name('news')->where(array('id'=>$id))->update($data);
            if($info){
                $this->success('新闻编辑成功',url('News/index'));
            }else{
                $this->success('新闻编辑失败',url('News/index'));
            }

        }
        $info = Db::name('news')->where(array("id"=>$id))->find();

        $type = Db::name('newstype')->select();
        $class = Db::name('news_class')->select();
        $xiangmu = Db::name('news_xiangmu')->select();

        $this->assign('info',$info);
        $this->assign('type',$type);
        $this->assign('newsClass',$class);
        $this->assign('newsXiangmu',$xiangmu);
        return $this->fetch();
    }

    public function addtype(){
        return $this->fetch();
    }

    //编辑新闻分类
    public function edittype(){
        $id = $this->request->param('id');
        $result = Db::name('newstype')->where(["id"=>$id])->find();
        if($this->request->isPost()){
            $data['typename'] = $this->request->param('newstype');
            $data['description'] = $this->request->param('description');
            $info = Db::name('newstype')->where(["id"=>$id])->update($data);
            if($info){
                $this->success('修改成功',url('News/typelist'));
            }else{
                $this->error('修改失败');
            }
        }

        $this->assign('result',$result);
        return $this->fetch();

    }

    public function typelist(){
        $data = Db::name('newstype')->order('id asc')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    //删除新闻
    public function delNews(){
        $id = $this->request->param('id');
        $info = Db::name('news')->where(array('id'=>$id))->delete();
        if($info){
            $this->success('删除成功');
        }else{
            $this->success('删除失败');
        }
    }

    //删除新闻分类
    public function deltype(){
        $id = $this->request->param('id');
        $info = Db::name('newstype')->where(array('id'=>$id))->delete();
        if($info){
            $this->success('删除成功',url('News/typelist'));
        }else{
            $this->error('删除失败');
        }
    }

    //新闻留言
    public function leaveMessage(){
        $data['phone'] = $this->request->param('phone');
        $pattern="/^1[34578]\d{9}$/";
        if(!preg_match($pattern,$data['phone'])){
            $this->error('手机号错误');
        }
        $data['nickname'] = $this->request->param('name');
        $data['posttime']=time();
        $data['ip']=$_SERVER['REMOTE_ADDR'];
        $info = Db::name('message')->insert($data);
        if($info){
            echo json_encode(['code'=>1,'message'=>'留言成功，我们会及时给您答复']);
        }else{
            echo  json_encode(['code'=>0,'message'=>'留言失败']);
        }
    }
}
