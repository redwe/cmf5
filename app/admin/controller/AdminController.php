<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Session;

/**
 * Class UserController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   => '管理组',
 *     'action' => 'default',
 *     'parent' => 'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   => '',
 *     'remark' => '管理组'
 * )
 */
class AdminController extends AdminBaseController
{

    /**
     * 管理员列表
     * @adminMenu(
     *     'name'   => '管理员',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员管理',
     *     'param'  => ''
     * )
     */
    /*public function index()
    {
        $where = ["u.checkadmin" => true];
        //搜索条件
        $userLogin = $this->request->param('username');
        $nickname = trim($this->request->param('nickname'));

        if ($userLogin) {
            $where['u.username'] = ['like', "%$userLogin%"];
        }

        $join = [
            ["department d","u.department=d.id","left"],
            ["role_user r","r.user_id=u.id","left"],
            ["role o","o.id=r.role_id","left"]
        ];
        $field = 'u.*,d.department as bumen,o.name as role_name';
        $users = Db::name('admin')->alias("u")->join($join)->field($field)
            ->where($where)
            ->order("u.id DESC")
            ->paginate(10);

        $users->appends(['username' => $userLogin, 'nickname' => $nickname]);
        // 获取分页显示
        $page = $users->render();

        $rolesSrc = Db::name('role')->select();
        $roles    = [];
        foreach ($rolesSrc as $r) {
            $roleId           = $r['id'];
            $roles["$roleId"] = $r;
        }
        $this->assign("page", $page);
        $this->assign("roles", $roles);
        $this->assign("users", $users);
        return $this->fetch();
    }*/

    public function index()
    {
        $group_id = Session::get("group_id");
        $where["a.checkadmin"] = true;

        $join = [
            ["department d","a.department=d.id","left"],
            ["role o","o.id=a.group_id","left"]
        ];
        $field = 'a.*,d.department as bumen,o.name as groupname';
        // 搜索条件

        if($group_id == 5){
            $groupData = array(["id" => "5","groupname" => "总监"]);
            $pid = cmf_get_current_admin_id();
            $where["a.id"] = $pid;
            $grouplist = Db::name('admin')->alias("a")->join($join)->field($field)->where($where)->find();
            $adminlist = Db::name('admin')->alias("a")->join($join)->field($field)->where($where)->select();
            $adminlist = json_decode($adminlist,true);
            foreach($adminlist as $k=>$v){
                $adminlist[$k]['groupname']= "销售员";
                $adminlist["groupname"] = "总监";
            }
            $grouplist["salers"] = $adminlist;
            $groupData[0]["groups"] = [$grouplist];
        }
        else
        {
            $pid = 1;
            $where2 = "id<>5 and id<>6";
            $groupData = Db::name('role')->where($where2)->select();
            $groupData = json_decode($groupData,true);
            foreach($groupData as $key=>$vo){
                $groupid = $vo['id'];
                $where3["a.group_id"] = $groupid;
                $where3["a.checkadmin"] = true;
                $adminlist = Db::name("admin")->alias("a")->join($join)->field($field)->where($where3)->select();
                $adminlist = json_decode($adminlist,true);
                $groupData[$key]["groups"] = $adminlist;
            }
            $salerlist = $this->getSalers($pid,5);
            array_push($groupData,array("id"=>5,"groupname"=>"总监","groups"=>$salerlist));
        }
        //$sql = Db::name("admin")->getLastSql();
        //dump($sql);
        $this->assign("groupid",$group_id);
        $this->assign('groupData',$groupData);
        return $this->fetch();
    }

    //管理员列表
    public function getSalers($pid=1,$group_id=5){

        $admindata = [];
        $where = array(
            "a.pid"=>$pid,
            "a.group_id"=>$group_id
        );
        $join = [
            ["department d","a.department=d.id","left"],
            ["role_user r","r.user_id=A.id","left"],
            ["role o","o.id=r.role_id","left"]
        ];
        $field = 'a.*,d.department as bumen,o.name as groupname';
        $admingroup = Db::name("admin")
            ->alias("a")
            ->join($join)
            ->field($field)
            ->where($where)
            ->select();
        $admingroup = json_decode($admingroup,true);
        foreach($admingroup as $key=>$vo){
            $groupid = $vo["id"];
            $role = Db::name("admin")->alias("a")->join($join)->field($field)
                ->where(array("a.pid"=>$groupid,"a.checkadmin"=>true))
                ->select();
            $role = json_decode($role,true);
            foreach($role as $key2=>$vo2){
                $adminid = $vo2['id'];
                $res = Db::name("admin")->alias("a")->join($join)->field($field)
                    ->where(array("a.pid"=>$adminid,"a.checkadmin"=>true))
                    ->select();
                //$grouplist = Db::name("role")->where(array("id"=>6))->find();
                $role[$key2]["groupname"] = "销售员"; //$grouplist['name'];
                $role[$key2]["members"] = $res;
            }
            $admingroup[$key]["salers"] = $role;
            $admingroup[$key]["groupname"] = "总监";
        }
        return $admingroup;
    }

    /**
     * 管理员添加
     * @adminMenu(
     *     'name'   => '管理员添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $dataList = Db::name("department")->where(array("status"=>1))->select();
        $this->assign("dataList",$dataList);

        $roles = Db::name('role')->where(['status' => 1])->order("id DESC")->select();
        $this->assign("roles", $roles);
        return $this->fetch();
    }

    /**
     * 管理员添加提交
     * @adminMenu(
     *     'name'   => '管理员添加提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员添加提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if ($this->request->isPost()) {

            if (!empty($_POST['role_id'])) {
                $role_ids = $_POST['role_id'];
                unset($_POST['role_id']);
                if (empty($_POST["username"]) || empty($_POST["password"])) {
                    $this->error('用户名和密码不能为空！');
                } else {
                    $_POST['password'] = cmf_password($_POST['password']);
                    $_POST['user_status'] = 1;
                    $_POST['group_id'] = $role_ids;
                    $_POST['pid'] = cmf_get_current_admin_id();
                    $result             = DB::name('admin')->insertGetId($_POST);
                    if ($result !== false) {
                        //$role_user_model=M("RoleUser");
                        /*//角色多选的处理
                        foreach ($role_ids as $role_id) {
                            if (cmf_get_current_admin_id() != 1 && $role_id == 1) {
                                $this->error("为了网站的安全，非网站创建者不可创建超级管理员！");
                            }
                            Db::name('RoleUser')->insert(["role_id" => $role_id, "user_id" => $result]);
                        }
                        */
                        Db::name('RoleUser')->insert(["role_id" => $role_ids, "user_id" => $result]);
                        $this->success("添加成功！", url("admin/index"));
                    } else {
                        $this->error("添加失败！");
                    }
                }
            } else {
                $this->error("请为此用户指定角色！");
            }

        }
    }

    /**
     * 管理员编辑
     * @adminMenu(
     *     'name'   => '管理员编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员编辑',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id    = $this->request->param('id', 0, 'intval');
        $roles = DB::name('role')->where(['status' => 1])->order("id DESC")->select();
        $this->assign("roles", $roles);
        $role_ids = DB::name('RoleUser')->where(["user_id" => $id])->column("role_id");
        $this->assign("role_ids", $role_ids);

        $dataList = Db::name("department")->where(array("status"=>1))->select();
        $this->assign("dataList",$dataList);

        $user = DB::name('admin')->where(["id" => $id])->find();
        $this->assign($user);
        return $this->fetch();
    }

    /**
     * 管理员编辑提交
     * @adminMenu(
     *     'name'   => '管理员编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员编辑提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        if ($this->request->isPost()) {
            if (!empty($_POST['role_id'])) {
                if (empty($_POST['password'])) {
                    unset($_POST['password']);
                } else {
                    $_POST['password'] = cmf_password($_POST['password']);
                }
                $role_ids = $this->request->param('role_id');
                unset($_POST['role_id']);
                if (empty($_POST["username"])) {
                    $this->error('用户名和密码不能为空！');
                } else {
                    $_POST['user_status'] = 1;
                    $_POST['group_id'] = $role_ids;
                    $result = DB::name('admin')->update($_POST);
                    if ($result !== false) {
                        $uid = $this->request->param('id', 0, 'intval');
                        DB::name("RoleUser")->where(["user_id" => $uid])->delete();
                        /*
                        foreach ($role_ids as $role_id) {
                            if (cmf_get_current_admin_id() != 1 && $role_id == 1) {
                                $this->error("为了网站的安全，非网站创建者不可创建超级管理员！");
                            }
                            DB::name("RoleUser")->insert(["role_id" => $role_id, "user_id" => $uid]);
                        }
                        */
                        DB::name("RoleUser")->insert(["role_id" => $role_ids, "user_id" => $uid]);
                        $this->success("保存成功！", url("admin/index"));
                    } else {
                        $this->error("保存失败！");
                    }
                }
            } else {
                $this->error("请为此用户指定角色！");
            }

        }
    }

    /**
     * 管理员个人信息修改
     * @adminMenu(
     *     'name'   => '个人信息',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员个人信息修改',
     *     'param'  => ''
     * )
     */
    public function userInfo()
    {
        $id   = cmf_get_current_admin_id();
        $user = Db::name('admin')->where(["id" => $id])->find();
        $this->assign($user);
        return $this->fetch();
    }

    /**
     * 管理员个人信息修改提交
     * @adminMenu(
     *     'name'   => '管理员个人信息修改提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员个人信息修改提交',
     *     'param'  => ''
     * )
     */
    public function userInfoPost()
    {
        if ($this->request->isPost()) {

            $data             = $this->request->post();
            $data['birthday'] = strtotime($data['birthday']);
            $data['id']       = cmf_get_current_admin_id();
            $create_result    = Db::name('admin')->update($data);;
            if ($create_result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

//添加部门
    public function add_ment(){
        if ($this->request->isPost()) {
            //$id = $this->request->param('id', 0, 'intval');
            $department = $this->request->param('department');
            $username = $this->request->param('username');
            $phone = $this->request->param('phone');

            if(empty($department)){
                $this->error("请输入部门名称！");
                exit();
            }

            $data["department"] = $department;
            $data["username"] = $username;
            $data["phone"] = $phone;
            $data["status"] = 1;
            $data["orders"] = 1;
            $data["create_time"] = time();

            $res = Db::name('department')->insert($data);
            if ($res !== false) {
                $this->success("保存成功！",url("admin/department"));
            } else {
                $this->error("保存失败！",url("admin/department"));
            }
        }
        else
        {
            return $this->fetch();
        }
    }


    //添加部门
    public function edit_ment(){
        if ($this->request->isPost()) {
            $id = $this->request->param('id', 0, 'intval');
            $department = $this->request->param('department');
            $username = $this->request->param('username');
            $phone = $this->request->param('phone');

            if(empty($department)){
                $this->error("请输入部门名称！");
                exit();
            }

            $data["department"] = $department;
            $data["username"] = $username;
            $data["phone"] = $phone;

            $where["id"] = $id;

            $res = Db::name('department')->where($where)->update($data);
            if ($res !== false) {
                $this->success("保存成功！",url("admin/department"));
            } else {
                $this->error("保存失败！",url("admin/department"));
            }
        }
        else
        {
            $id = $this->request->param('id', 0, 'intval');
            $where = array(
                "id"=>$id,
                "status"=>1
            );
            $departs = DB::name('department')->where($where)->find();
            $this->assign('departs',$departs);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }


    //部门列表
    public function department(){
        $departs = DB::name('department')->where(["status" => 1])->select();
        $this->assign('departs',$departs);
        return $this->fetch();
    }

    public function del_ment(){
        $id = $this->request->param('id', 0, 'intval');
        $data = array("status"=>0);
        $res = Db::name('department')->where(array("id"=>$id))->update($data);
        if ($res !== false) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 管理员删除
     * @adminMenu(
     *     'name'   => '管理员删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id == 1) {
            $this->error("最高管理员不能删除！");
        }

        if (Db::name('admin')->delete($id) !== false) {
            Db::name("RoleUser")->where(["user_id" => $id])->delete();
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 停用管理员
     * @adminMenu(
     *     'name'   => '停用管理员',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '停用管理员',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (!empty($id)) {
            $result = Db::name('admin')->where(["id" => $id, "user_type" => 1])->setField('user_status', '0');
            if ($result !== false) {
                $this->success("管理员停用成功！", url("admin/index"));
            } else {
                $this->error('管理员停用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 启用管理员
     * @adminMenu(
     *     'name'   => '启用管理员',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '启用管理员',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (!empty($id)) {
            $result = Db::name('admin')->where(["id" => $id, "user_type" => 1])->setField('user_status', '1');
            if ($result !== false) {
                $this->success("管理员启用成功！", url("admin/index"));
            } else {
                $this->error('管理员启用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }
}