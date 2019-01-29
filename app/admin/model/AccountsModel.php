<?php
namespace app\admin\model;

use think\Model;
use think\DB;
use think\Session;

class AccountsModel extends Model
{
    //资金账单明细
    public function add_accounts($uid, $change_accounts, $type ,$action_id='')
    {
        $account = Db::name('accounts')->where(['uid'=>$uid])->find();
        if (!$account){
            $data['uid']=$uid;
            $data['accounts']=0;
            $rs=Db::name('accounts')->insert($data);
            if(!$rs){
                return ['code'=>500,'msg'=>'系统在开小差,请稍后再试~!'];
            }
        }
        $data = [];
        $data['uid'] = $uid;
        $data['action_id'] = $action_id;
        $data['change_num'] = $change_accounts;
        $data['change_before'] = isset($account['accounts'])?$account['accounts']:0;
        $data['change_after'] = $change_accounts + $data['change_before'];
        if($type==2){
            $data['change_after'] = $data['change_before']-$change_accounts;
            if ($data['change_after']<0){
                return ['code'=>500,'msg'=>'您的账户余额不足,请及时联系管理员充值!'];
            }
        }
        $data['type'] = $type;
        $data['time'] = time();
        $this->startTrans();
        try {
            $param["accounts"] = $data['change_after'];
            Db::name('accounts')->where(['uid'=>$uid])->update($param);
            $result = Db::name('accounts_log')->insert($data);
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            return ['code'=>500,'msg'=>'系统在开小差,请稍后再试~!'];
        }
    }
}