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
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Session;
use app\admin\model\Upload;

class ArticleInfoController extends AdminBaseController
{
    public function add_article(){

        if ($this->request->isPost()) {

            $title  = $this->request->param('title');
            $remark  = $this->request->param('remark');
            $content  = $this->request->param('content');
            $department  = $this->request->param('department');
            $types = $this->request->param("types");

            if(!empty($title) && !empty($content))
            {
                $param['title'] = $title;
                $param['remark'] = $remark;
                $param['content'] = $content;
                $param['department'] = $department;
                $param['types'] = $types;
                $param['status'] = 1;
                $param['datetime'] = time();
                $param['admin_id'] = cmf_get_current_admin_id();
                //上传文件

                $updata = new Upload();
                $url = '/upload/news/';
                $array = ["jpg","gif","png"];
                $uploads = $updata->uploadpic('thumb',$url,$array);
                    if ($uploads['res']) {
                        $param['thumb'] = $uploads['data'];
                    }
                $rs = Db::name('articles')->insert($param);
                if ($rs) {
                    $this->success('公告信息发布成功！',url("Article_info/lists"));
                } else {
                    $this->error('系统异常，请稍后提交！');
                }
            }

        }
        else
        {
            //$department = C("DEPARTMENT");
            $departid = Session::get('department');
            if(empty($departid)){
                $this->error('您所在的部门无法发布信息！');
            }
            else
            {
                $department = Db::name("department")->where(array("status"=>1,"id"=>$departid))->find();
                $this->assign("depart",$department);
                return $this->fetch();
            }
        }

    }

    public function lists(){
        //dump(date("Y-m-d h:i:s",1558848904));
        $where["a.status"] = 1;
        $join = [
            ["department d","d.id=a.department"],
            ["admin m","m.id=a.admin_id"]
        ];
        $field = "a.*,d.department,m.username";
        $departid = Session::get('department');
        $group_id = Session::get('group_id');
        if($group_id > 1){
            $where["a.department"] = $departid;
        }
        $result = Db::name('articles')
            ->alias("a")->join($join)->field($field)
            ->where($where)->order("a.id desc")
            ->paginate(10);
        //$sql = M('articles')->getLastSql();
        //dump($sql);
        $this->assign("result",$result);
        $this->assign('page', $result->render());
        return $this->fetch();
    }


    public function delete(){
        $id = $this->request->param("id");
        $where["id"] = $id;
        $data["status"] = 0;
        $result = Db::name('articles')->where($where)->update($data);
        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('系统异常，请稍后提交！');
        }
    }

    public function edit(){

        if ($this->request->isPost()) {

            $id = $this->request->param("id");

            $title  = $this->request->param('title');
            $remark  = $this->request->param('remark');
            $content  = $this->request->param('content');
            $bumen  = $this->request->param('department');
            $types = $this->request->param("types");

            $param['title'] = $title;
            $param['remark'] = $remark;
            $param['content'] = $content;
            $param['department'] = $bumen;
            $param['types'] = $types;
            $param['admin_id'] = cmf_get_current_admin_id();
            //上传文件
            if($_FILES){
                $updata = new Upload();
                $url = '/upload/news/';
                $array = ["jpg","gif","png"];
                $uploads = $updata->uploadpic('thumb',$url,$array);
                if ($uploads['res']) {
                    $param['thumb'] = $uploads['data'];
                }
            }
            $where["id"] = $id;
            $where["status"] = 1;

            //dump($param);

            $result = Db::name('articles')->where($where)->update($param);
            if ($result) {
                $this->success('编辑成功！',url("Article_info/lists"));
            } else {
                $this->error('系统异常，请稍后提交！');
            }
        }else{
            $id = $this->request->param("id");
            $where["a.id"] = $id;
            $departid = Session::get('department');

            $where["a.status"] = 1;
            $join = [
                ["department d","d.id=a.department"],
                ["admin m","m.id=a.admin_id"]
            ];
            $field = "a.*,d.department as bumen,m.username";
            $res = Db::name('articles')->alias("a")->join($join)->field($field)->where($where)->find();

            $department = Db::name("department")->where(array("status"=>1,"id"=>$departid))->find();
            $this->assign("depart",$department);
            //dump($res);
            $this->assign("res",$res);
            return $this->fetch();
        }
    }

}
