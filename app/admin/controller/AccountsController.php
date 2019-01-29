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
use app\admin\model\AdminMenuModel;
use think\Session;
use app\admin\model\AccountsModel;

class AccountsController extends AdminBaseController
{
    //资金管理首页
    public function index()
    {
        $username = $this->request->param('username');
        $export = $this->request->param('export');
        $type_id = $this->request->param('type');

        $group_id = Session::get("group_id");

        if($group_id != 1){
            $where['c.uid'] = cmf_get_current_admin_id();
        }else {
            $where = [];
        }
        if ($username && $username != " ") {
            $where['a.username']= array('like',"%{$username}%") ;
        }
        $join = [
            ["admin a","a.id=c.uid"]
        ];
        $field = "c.*,a.username";
        //导出
        if ($export == 'yes') {
            $list = Db::name('accounts')->alias("c")->field($field)->join($join)->where($where)->select();
            header("Content-type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=资金管理账单信息".time().".csv");
            $data  = "ID\t";
            $data .= "管理员\t";
            $data .= "资金数量\t";
            $data .="\t\n";

            foreach ($list as $k => $v) {
                $data .=$v['id']."\t";
                $data .=$v['username']."\t";
                $data .=$v['accounts']."\t";
                $data .="\t\n";
            }
            echo(chr(255).chr(254));
            echo(mb_convert_encoding($data,"UTF-16LE","UTF-8"));
            exit;
        }
        $accounts = Db::name('accounts')->alias("c")->field($field)->join($join)->where($where)
            ->order('c.id desc')
            ->paginate(20);
        $pages = $accounts->render();
        //同步数据
        $admin_data = Db::name('admin')->where(array('group_id'=>5))->select();
        if($admin_data){
            foreach ($admin_data as $key=>$val){
                $find = Db::name('accounts')->where(array('uid'=>$val['id']))->find();
                if(empty($find)){
                    Db::name('accounts')->insert(array('uid'=>$val['id'],'accounts'=>0));
                }
            }
        }
        $this->assign('accounts', $accounts);
        $this->assign('type_id',$type_id);
        $this->assign('page', $pages);// 赋值分页输出
        return $this->fetch();
    }

    //资金管理明细列表
    public function lists(){

        $type_id=$this->request->param('type');
        $username=$this->request->param('username');
        $export=$this->request->param('export');
        $create_time=strtotime($this->request->param('create_time'));
        $create_time_=strtotime($this->request->param('create_time_'));

        $group_id = Session::get("group_id");

        if($group_id != 1){
            $where['c.uid'] = cmf_get_current_admin_id();
        }else {
            $where = [];
        }
        $type=[1=>'管理员充值',2=>'购买课程',3=>'退款'];
        if ($type_id && $type_id != ""){
            $where ['c.type']= $type_id;
        }
        if ($username && $username != ''){
            $where['a.username']= array('like',"%{$username}%") ;
        }
        if ($create_time && $create_time !="") {
            $where['c.time'] = ['egt',$create_time];
            if ($create_time_!= '') {
                $where['c.time'] = [['egt',$create_time],['elt',$create_time_. " 23:59:59'"],'and'];
            }
        }
        $tt = '';
        $join = [
            ["admin a","a.id = c.uid"]
        ];
        $field = 'c.*,a.username';
        //导出
        if ($export == 'yes') {
            $list = Db::name('accounts_log')->alias("c")->join($join)->field($field)->where($where)->order('time desc')->select();

            header("Content-type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=资金管理明细信息".time().".csv");
            $data  = "ID\t";
            $data .= "管理员\t";
            $data .= "充值金额\t";
            $data .= "充值前的金额\t";
            $data .= "充值后的金额\t";
            $data .= "类型\t";
            $data .= "时间\t";
            $data .="\t\n";

            foreach ($list as $k => $v) {
                $data .=$v['id']."\t";
                $data .=$v['username']."\t";
                $data .=$v['change_num']."\t";
                $data .=$v['change_before']."\t";
                $data .=$v['change_after']."\t";
                $data .=$type[$v['type']]."\t";
                $data .=date('Y-m-d H:i:s',$v['time'])."\t";
                $data .="\t\n";
            }
            echo(chr(255).chr(254));
            echo(mb_convert_encoding($data,"UTF-16LE","UTF-8"));
            exit;
        }
        $accounts_log = Db::name('accounts_log')->alias("c")->join($join)->field($field)
            ->where($where)->order('c.time desc')
            ->paginate(20);
        $page = $accounts_log->render();
        switch ($type_id){
            case 1:$tt = '充值';break;
            case 2:$tt = '购买';break;
            case 3:$tt = '退款';break;
        }
        $this->assign('tt',$tt);
        $this->assign('type',$type);
        $this->assign('type_id',$type_id);
        $this->assign('page',$page);
        $this->assign('accounts_log',$accounts_log);
        return $this->fetch();
    }

    //充值
    public function edit(){
        $id=$this->request->param('id');
        if ($this->request->isPost()) {
            $uid=$this->request->param('uid');
            $change_accounts=$this->request->param('accounts');
            $setAcount = new AccountsModel();
            $setAcount->add_accounts($uid,$change_accounts,1);
            $this->success('充值成功',url('accounts/index'));
        }else{
            $join = [
                ["admin a","a.id = c.uid"]
            ];
            $field = 'c.*,a.username';
            $account = Db::name('accounts')
                ->alias("c")->field($field)->join($join)
                ->where(array('c.id'=>$id))
                ->find();
            $this->assign('account',$account);
            return $this->fetch();
        }
    }

    //查看个人所有账单信息
    public function look_accounts(){
        $id=$this->request->param('id');
        $type_id=$this->request->param('type');
        $export=$this->request->param('export');

        $group_id = Session::get("group_id");

        if($group_id != 1){
            $where['uid'] = cmf_get_current_admin_id();
        }else {
            $where = [];
        }
        $type=[1=>'管理员充值',2=>'购买课程',3=>'退款'];
        if ($type_id && $type_id != ""){
            $where ['jy_accounts_log.type']= $type_id;
        }
        $where['jy_accounts_log.uid']=$id;
        //导出
        if ($export == 'yes') {
            $list = Db::name('accounts_log')
                ->field('jy_accounts_log.*,jy_admin.username')
                ->join('jy_admin ON jy_accounts_log.uid=jy_admin.id','left')
                ->where($where)
                ->select();

            header("Content-type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=个人资金账单明细".time().".csv");
            $data  = "ID\t";
            $data .= "管理员\t";
            $data .= "充值金额\t";
            $data .= "充值前的金额\t";
            $data .= "充值后的金额\t";
            $data .= "类型\t";
            $data .= "时间\t";
            $data .="\t\n";

            foreach ($list as $k => $v) {
                $data .=$v['id']."\t";
                $data .=$v['username']."\t";
                $data .=$v['change_num']."\t";
                $data .=$v['change_before']."\t";
                $data .=$v['change_after']."\t";
                $data .=$type[$v['type']]."\t";
                $data .=date('Y-m-d H:i:s',$v['time'])."\t";
                $data .="\t\n";
            }
            echo(chr(255).chr(254));
            echo(mb_convert_encoding($data,"UTF-16LE","UTF-8"));
            exit;
        }
        $accounts_info = Db::name('accounts_log')
            ->field('jy_accounts_log.*,jy_admin.username')
            ->join('jy_admin ON jy_accounts_log.uid=jy_admin.id','left')
            ->where($where)
            ->select();
        $type=[1=>'管理员充值',2=>'购买课程',3=>'退款'];
        $this->assign('type',$type);
        $this->assign('accounts_info',$accounts_info);
        return $this->fetch();
    }

}
