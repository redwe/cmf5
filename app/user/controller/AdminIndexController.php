<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------

namespace app\user\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\user\model\UserModel;
use app\admin\model\Upload;
use think\Session;

/**
 * Class AdminIndexController
 * @package app\user\controller
 *
 * @adminMenuRoot(
 *     'name'   =>'用户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10,
 *     'icon'   =>'group',
 *     'remark' =>'用户管理'
 * )
 *
 * @adminMenuRoot(
 *     'name'   =>'用户组',
 *     'action' =>'default1',
 *     'parent' =>'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'',
 *     'remark' =>'用户组'
 * )
 */
class AdminIndexController extends AdminBaseController
{

    /**
     * 后台本站用户列表
     * @adminMenu(
     *     'name'   => '本站用户',
     *     'parent' => 'default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户',
     *     'param'  => ''
     * )
     */
    /*public function index()
    {
        $where   = [];
        $request = input('request.');

        $where['user_type'] = 2;
        if (!empty($request['uid'])) {
            $where['id'] = intval($request['uid']);
        }
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];

            $keywordComplex['user_login|user_nickname|user_email|mobile']    = ['like', "%$keyword%"];
        }
        $usersQuery = Db::name('member');

        $list = $usersQuery->whereOr($keywordComplex)->where($where)->order("create_time DESC")->paginate(10);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }*/

    public function index(){
        $where['status'] = array("gt",-1);
        $request = input('request.');
        if (!empty($request['uid'])) {
            $where['id'] = intval($request['uid']);
        }
        $group_id = Session::get("group_id");
        if($group_id)
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];
            $keywordComplex['username|phone|email']    = ['like', "%$keyword%"];
        }
        $userObj = new UserModel();
        $list = Db::name('member')->whereOr($keywordComplex)->where($where)->order("id DESC")->paginate(20);
        //$list = json_decode($list,true);
        $datalist = [];
        foreach ($list as $k=>$v){
            $v['group'] = $userObj->getGroupByExp($v['expval']);
            array_push($datalist,$v);
        }

        $page = $list->render();
        $this->assign('list', $datalist);
        $this->assign('page', $page);
        return $this->fetch();
    }


    public function add(){
        if ($this->request->isPost()) {
            $username = $this->request->param('username');
            $password = $this->request->param('password');
            $repassword = $this->request->param('repassword');
            $phone = $this->request->param('phone');
            $email = $this->request->param('email');
            $expval = $this->request->param('expval');
            if(empty($username)){
                $this->error('账户不能为空');
            }
            if(empty($password)){
                $this->error('密码不能为空');
            }
            if(empty($repassword)){
                $this->error('确认密码不能为空');
            }
            if(empty($phone)){
                $this->error('手机号不能为空');
            }
            if($password != $repassword){
                $this->error('两次密码不一致');
            }
            if(!isPhone($phone)){
                $this->error('请输入正确的手机号');
            }
            //查询用户是否重复  用户名+手机号验证
            $data = Db::name("member")->where(array('username'=>$username))->whereOr(array('phone'=>$phone))->find();
            if($data){
                $this->error('用户已存在');
            }
            $save_data = array(
                'username'=>$username,
                'password'=>md5(md5($password)),
                'phone'=>$phone,
                'email'=>$email,
                'expval'=>$expval,
                'regip'=>get_client_ip(),
                'regtime'=>time(),
                'status' => 1
            );
            if(Db::name("member")->insert($save_data)){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
            exit();
        }
        return $this->fetch();
    }

    public function edit(){
        $id = $this->request->param('id');
        if(empty($id)){
            $this->error('参数错误');
        }
        $data = Db::name("member")->where(array("id"=>$id))->find($id);
        if(empty($data)){
            $this->error('暂无数据');
        }
        if ($this->request->isPost()) {
            $username = $this->request->param('username');
            $password = $this->request->param('password');
            $repassword = $this->request->param('repassword');
            $phone = $this->request->param('phone');
            $email = $this->request->param('email');
            $expval = $this->request->param('expval');
            $cardno = $this->request->param('cardno');
            $province = $this->request->param('province');
            $address = $this->request->param('address');
            $company = $this->request->param('company');

            if(empty($username)){
                $this->error('账户不能为空');
            }
            if(empty($phone)){
                $this->error('手机号不能为空');
            }
            if(!isPhone($phone)){
                $this->error('请输入正确的手机号');
            }
            $where2="id!=:id and (phone=:phone or username=:username)";
            //查询用户是否重复  用户名+手机号验证
            $data = Db::name("member")->where($where2)->bind(array('id'=>$id,"phone"=>$phone,"username"=>$username))->find();
            if($data){
                $this->error('用户已存在');
            }

            $save_data = array(
                'username'=>$username,
                'phone'=>$phone,
                'email'=>$email,
                'cardnum' => $cardno,
                'expval'=>$expval,
                'province'=>$province,
                'address'=>$address,
                'company'=>$company
            );
            if(!empty($password)){
                if(empty($repassword)){
                    $this->error('确认密码不能为空');
                }
                if($password != $repassword){
                    $this->error('两次密码不一致');
                }
                $save_data['password'] = cmf_password($password);        //md5(md5($password));
            }
            $updata = new Upload();
            if(request()->file('card_face')){
                $uploads = $updata->uploadpic('card_face');
                if ($uploads['res']) {
                    $save_data['card_face'] = $uploads['data'];
                }
            }
            //上传文件身份证反面
            if(request()->file('card_back')) {
                $uploads = $updata->uploadpic('card_back');
                if ($uploads['res']) {
                    $save_data['card_back'] = $uploads['data'];
                }
            }
            if(Db::name("member")->where(array("id"=>$id))->update($save_data)){
                $this->success('编辑成功');
            }else{
                $this->error('编辑失败');
            }
            exit();
        }
        $this->assign('data',$data);
        return $this->fetch();
    }



    public function del(){
        $id = $this->request->param('id');
        if(empty($id)){
            $this->error('参数错误');
        }
        $data = Db::name("member")->where(array("id"=>$id))->select();
        if(empty($data)){
            $this->error('暂无数据');
        }
        if(Db::name("member")->where(array("id"=>$id))->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /**
     * 本站用户拉黑
     * @adminMenu(
     *     'name'   => '本站用户拉黑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户拉黑',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            $result = Db::name("member")->where(["id" => $id])->setField('status', 0);
            if ($result) {
                $this->success("会员拉黑成功！", "adminIndex/index");
            } else {
                $this->error('会员拉黑失败,会员不存在,或者是管理员！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 本站用户启用
     * @adminMenu(
     *     'name'   => '本站用户启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户启用',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            Db::name("member")->where(["id" => $id])->setField('status', 1);
            $this->success("会员启用成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }
}
