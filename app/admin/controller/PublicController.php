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
use think\Route;

class PublicController extends AdminBaseController
{
    public function _initialize()
    {

    }

    /**
     * 后台登陆界面
     */
    public function login()
    {
        $loginAllowed = session("__LOGIN_BY_CMF_ADMIN_PW__");
        if (empty($loginAllowed)) {
            //$this->error('非法登录!', cmf_get_root() . '/');
            return redirect(cmf_get_root() . "/");
        }

        $admin_id = session('ADMIN_ID');
        if (!empty($admin_id)) {//已经登录
            return redirect(url("admin/Index/index"));
        } else {
            $site_admin_url_password = config("cmf_SITE_ADMIN_URL_PASSWORD");
            $upw                     = session("__CMF_UPW__");
            if (!empty($site_admin_url_password) && $upw != $site_admin_url_password) {
                return redirect(cmf_get_root() . "/");
            } else {
                session("__SP_ADMIN_LOGIN_PAGE_SHOWED_SUCCESS__", true);
                $result = hook_one('admin_login');
                if (!empty($result)) {
                    return $result;
                }
                return $this->fetch(":login");
            }
        }
    }

    /**
     * 登录验证
     */
    public function doLogin()
    {
        if (hook_one('admin_custom_login_open')) {
            $this->error('您已经通过插件自定义后台登录！');
        }

        $loginAllowed = session("__LOGIN_BY_CMF_ADMIN_PW__");
        if (empty($loginAllowed)) {
            $this->error('非法登录!', cmf_get_root() . '/');
        }
        /*
               $captcha = $this->request->param('captcha');
               if (empty($captcha)) {
                   $this->error(lang('CAPTCHA_REQUIRED'));
               }

               //验证码
               if (!cmf_captcha_check($captcha)) {
                   $this->error(lang('CAPTCHA_NOT_RIGHT'));
               }
       */
        $name = $this->request->param("username");
        if (empty($name)) {
            $this->error(lang('USERNAME_OR_EMAIL_EMPTY'));
        }
        $pass = $this->request->param("password");
        if (empty($pass)) {
            $this->error(lang('PASSWORD_REQUIRED'));
        }
        $where['nickname'] = $name;
        $result = Db::name('admin')->where($where)->find();

        if (!empty($result)) {
            if (cmf_compare_password($pass, $result['password'])) {
                $groups = Db::name('RoleUser')
                    ->alias("a")
                    ->join('__ROLE__ b', 'a.role_id =b.id')
                    ->where(["user_id" => $result["id"], "status" => 1])
                    ->value("role_id");
                if ($result["id"] != 1 && (empty($groups) || empty($result['user_status']))) {
                    $this->error(lang('USE_DISABLED'));
                }
                $groupid = $result["group_id"];
                $pid = $result["pid"];
                $username = $result["username"];
                $badmin = Db::name("admin")->where(array("id"=>$pid))->find();
                //dump($badmin);
                if($groupid == 6){
                    $check_name = $badmin['username'];
                }
                else
                {
                    $check_name = $username;
                }

                //登入成功页面跳转
                session('ADMIN_ID', $result["id"]);
                session('name', $username);
                session('check_name', $check_name);
                session('group_id', $groupid);
                session('department', $result["department"]);

                $result['loginip']   = get_client_ip(0, true);
                $result['logintime'] = time();
                $token                     = cmf_generate_user_token($result["id"], 'web');
                if (!empty($token)) {
                    session('token', $token);
                }
                Db::name('admin')->update($result);
                cookie("admin_username", $name, 3600 * 24 * 30);
                session("__LOGIN_BY_CMF_ADMIN_PW__", null);
                $this->success(lang('LOGIN_SUCCESS'), url("admin/Index/index"));
            } else {
                $this->error(lang('PASSWORD_NOT_RIGHT'));
            }
        } else {
            $this->error(lang('USERNAME_NOT_EXIST'));
        }
    }

    /**
     * 后台管理员退出
     */
    public function logout()
    {
        session('ADMIN_ID', null);
        return redirect(url('/admin', [], false, true));
    }
}