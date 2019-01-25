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

/**
 * Class AdminIndexController
 * @package app\portal\controller
 * @adminMenuRoot(
 *     'name'   =>'门户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 30,
 *     'icon'   =>'th',
 *     'remark' =>'门户管理'
 * )
 */
class OrdersController extends AdminBaseController
{

    public function index(){

        $where['o.status']=1;
        $join = [
            ["carts c","c.order_id = o.order_code"],
            ["goods g","c.goods_id = g.id"],
            ["member m","m.id = o.member_id"],
        ];
        $field = 'o.*,g.title,c.kc_year,m.username,m.phone';
        $result = Db::name("orders")->alias("o")
            ->join($join)->field($field)
            ->where($where)
            ->order("o.id DESC")
            ->paginate(20);

        $this->assign('result', $result);
        $this->assign('page', $result->render());
        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

}
