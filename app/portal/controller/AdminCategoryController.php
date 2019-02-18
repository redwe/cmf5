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
namespace app\portal\controller;

use app\admin\model\RouteModel;
use cmf\controller\AdminBaseController;
use app\portal\model\PortalCategoryModel;
use think\Db;
use app\admin\model\ThemeModel;
use PHPExcel;
use think\Request;


class AdminCategoryController extends AdminBaseController
{
    /**
     * 文章分类列表
     * @adminMenu(
     *     'name'   => '分类管理',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章分类列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $content = hook_one('portal_admin_category_index_view');

        if (!empty($content)) {
            return $content;
        }

        $portalCategoryModel = new PortalCategoryModel();
        $keyword             = $this->request->param('keyword');

        if (empty($keyword)) {
            $categoryTree = $portalCategoryModel->adminCategoryTableTree();
            $this->assign('category_tree', $categoryTree);
        } else {
            $categories = $portalCategoryModel->where('name', 'like', "%{$keyword}%")
                ->where('delete_time', 0)->select();
            $this->assign('categories', $categories);
        }

        $this->assign('keyword', $keyword);

        return $this->fetch();
    }

    /**
     * 添加文章分类
     * @adminMenu(
     *     'name'   => '添加文章分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加文章分类',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $content = hook_one('portal_admin_category_add_view');

        if (!empty($content)) {
            return $content;
        }

        $parentId            = $this->request->param('parent', 0, 'intval');
        $portalCategoryModel = new PortalCategoryModel();
        $categoriesTree      = $portalCategoryModel->adminCategoryTree($parentId);

        $themeModel        = new ThemeModel();
        $listThemeFiles    = $themeModel->getActionThemeFiles('portal/List/index');
        $articleThemeFiles = $themeModel->getActionThemeFiles('portal/Article/index');

        $this->assign('list_theme_files', $listThemeFiles);
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('categories_tree', $categoriesTree);
        return $this->fetch();
    }

    /**
     * 添加文章分类提交
     * @adminMenu(
     *     'name'   => '添加文章分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加文章分类提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $portalCategoryModel = new PortalCategoryModel();

        $data = $this->request->param();

        $result = $this->validate($data, 'PortalCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $result = $portalCategoryModel->addCategory($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('AdminCategory/index'));

    }

    //添加课时
    public function add_class(){
        $id = $this->request->param('id');

        if(empty($id)){
            $this->error('参数错误');
        }
        $data = Db::name("portal_category")->where(array("id"=>$id))->find();
        if(empty($data)){
            $this->error('分类错误');
        }

        if($this->request->isPost()){

            $save_data = [];
            $ks = $_POST['ks'];
            $ksd = $_POST['ksd'];
            $path = $data['path'];
            $tm = time();
            $s = 0;
            //先删除原来的课时数据
            Db::name("post_lession")->where(array('typeid'=>$id))->delete();

            for ($i=0;$i<count($ks);$i++){
                $save_data[$i] = array(
                    'typeid'=>$id,
                    'typestr'=>$path,
                    'status'=>1,
                    'created_time'=>$tm,

                );
                $save_data[$i]['lession_name'] = $ks[$i+1];
                $save_data[$i]['lession_url'] = $ksd[$i+1];
                $s = Db::name("post_lession")->insert($save_data[$i]);
            }
            //$s = Db::name("post_lession")->insert($save_data);

            if($s){
                $this->success('添加课时成功',url("portal/admin_category/add_class",array("id"=>$id)));
            }else{
                $this->error('添加课时失败',url("portal/admin_category/add_class",array("id"=>$id)));
            }

            exit();
        }

        $ks_num = 1;
        //查询改分类下面是否存在课时
        $ks_data = Db::name("post_lession")->where(array('typeid'=>$id))->order('id asc')->select();
        if($ks_data){
            $ks_num = count($ks_data)+1;
        }
        $this->assign('ks_data',$ks_data);
        $this->assign('ks_num',$ks_num);

        $this->assign('data', $data);
        $this->assign('id', $id);
        return $this->fetch();
    }

//导入课时
    public function import_class(){
        $id = $this->request->param('id');

        if(empty($id)){
            $this->error('参数错误');
        }
        $data = Db::name("portal_category")->where(array("id"=>$id))->find();
        if(empty($data)){
            $this->error('分类错误');
        }

        if($this->request->isPost()){
            // exl格式，否则重新上传
            // if($files['type'] !='application/vnd.ms-excel'){
            //     $this->error('不是Excel文件，请重新上传');
            // }
            // 上传
            $s = 0;
            $typestr = $data['path'];
            $file = request()->file('exl');
            //$rootUrl = $_SERVER['DOCUMENT_ROOT'];
            if($file){
                $savePath = ROOT_PATH . 'public' . DS . 'upload/excel/';
                $info = $file->move($savePath);
                if($info){
                    // 成功上传后 获取上传信息
                    // 输出 jpg
                    $exts = $info->getExtension();
                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                    $file_path = $info->getSaveName();
                    // 输出 42a79759f284b767dfcb2a0197904287.jpg
                    $file_name = $info->getFilename();

                    $objPHPExcel = new \PHPExcel();
                    $path = $savePath.$file_path;
                    //文件的扩展名
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    if ($ext == 'xlsx') {
                        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
                        $objPHPExcel = $objReader->load($path, 'utf-8');
                    } elseif ($ext == 'xls') {
                        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                        $objPHPExcel = $objReader->load($path, 'utf-8');
                    }

                    $sheet = $objPHPExcel->getSheet(0);
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    $highestColumn = $sheet->getHighestColumn(); // 取得总列数
                    $ar = array();
                    $i = 0;
                    $importRows = 0;

                    //先删除原来的课时数据
                    Db::name("post_lession")->where(array('typeid'=>$id))->delete();

                    $datas['typeid'] = $id;   //需要导入的分类id
                    $datas['status'] = 1;
                    $datas['created_time'] = time();
                    $datas['typestr'] = $typestr;

                    for ($j = 2; $j <= $highestRow; $j++) {
                        $importRows++;

                        $datas['lession_name'] = (string)$objPHPExcel->getActiveSheet()->getCell("A$j")->getValue();   //需要导入的媒体名称
                        $datas['lession_url'] = (string)$objPHPExcel->getActiveSheet()->getCell("B$j")->getValue();   //需要导入的媒体地址
                        $datas['mediaid'] = (string)$objPHPExcel->getActiveSheet()->getCell("C$j")->getValue();   //需要导入的媒体ID
                        $datas['timelength'] = (string)$objPHPExcel->getActiveSheet()->getCell("D$j")->getValue();//需要导入的媒体时长

                        $s = Db::name("post_lession")->insert($datas);
                    }
                }else{
                    // 上传失败获取错误信息
                    $msg = $file->getError();
                }
            }

            if($s){
                $this->success('添加课时成功');
            }else{
                $this->error('添加课时失败');
            }
            exit();
        }

        $this->assign('data', $data);
        $this->assign('id', $id);
        return $this->fetch();
    }


    //添加讲义
    public function addjy(){

        $id = $this->request->param('id');
        $type = $this->request->param('type');

        if(empty($type)){
            $type = 1;
        }

        if(empty($id)){
            $this->error('参数错误');
        }
        $data = Db::name("portal_category")->where(array("id"=>$id))->find();
        if(empty($data)){
            $this->error('分类错误');
        }

        if($this->request->isPost()) {
            $s = 0;
            $ks = [];
            $old = '';
            $k1 = 0;
            $k2 = 0;
            if (isset($_POST['ks'])) {
                $ks = $_POST['ks'];
                $k1 = count($ks);
            }
            if (isset($_POST['old'])) {
                $old = $_POST['old'];
                $k2 = count($old);
            }
            Db::name('jylist')->where(array('tid' => $id))->delete();
            for ($i = 0; $i < $k1; $i++) {
                    $save_data[$i] = array(
                        'tid' => $id,
                        'type' => $type,
                        'create_time' => date('Y-m-d H:i:s'),
                    );
                    $save_data[$i]['name'] = $ks[$i + 1];

                    $file = request()->file('ksd' . ($i + 1));
                    if ($file) {
                        $savePath = ROOT_PATH . 'public' . DS . '/upload/jianyi/';
                        $info = $file->move($savePath);
                        $fileurl = $info->getSaveName();
                        $fileurl = str_replace("\\","/",$fileurl);
                        $save_data[$i]['url'] = 'jianyi/' . $fileurl;
                    }
                    else
                    {
                        $save_data[$i]['url'] = $old[$i + 1];
                    }
                    //先删除原来的课时数据
                    $s = Db::name('jylist')->insert($save_data[$i]);
              }
                if ($s)
                {
                    $this->success('添加成功');
                }
                else
                {
                    $this->error('添加失败');
                }
            exit();
        }
        $ks_num = 1;
        //查询改分类下面是否存在课时
        $ks_data = Db::name('jylist')->where(array('tid'=>$id))->order('id asc')->select();
        if($ks_data){
            $ks_num = count($ks_data)+1;
        }
        $this->assign('type',$type);
        $this->assign('ks_data',$ks_data);
        $this->assign('ks_num',$ks_num);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 编辑文章分类
     * @adminMenu(
     *     'name'   => '编辑文章分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑文章分类',
     *     'param'  => ''
     * )
     */
    public function edit()
    {

        $content = hook_one('portal_admin_category_edit_view');

        if (!empty($content)) {
            return $content;
        }

        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $category = PortalCategoryModel::get($id)->toArray();

            $portalCategoryModel = new PortalCategoryModel();
            $categoriesTree      = $portalCategoryModel->adminCategoryTree($category['parent_id'], $id);

            $themeModel        = new ThemeModel();
            $listThemeFiles    = $themeModel->getActionThemeFiles('portal/List/index');
            $articleThemeFiles = $themeModel->getActionThemeFiles('portal/Article/index');

            $routeModel = new RouteModel();
            $alias      = $routeModel->getUrl('portal/List/index', ['id' => $id]);

            $category['alias'] = $alias;
            $this->assign($category);
            $this->assign('list_theme_files', $listThemeFiles);
            $this->assign('article_theme_files', $articleThemeFiles);
            $this->assign('categories_tree', $categoriesTree);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑文章分类提交
     * @adminMenu(
     *     'name'   => '编辑文章分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑文章分类提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'PortalCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $portalCategoryModel = new PortalCategoryModel();

        $result = $portalCategoryModel->editCategory($data);

        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!','portal/admin_category/index');
    }

    /**
     * 文章分类选择对话框
     * @adminMenu(
     *     'name'   => '文章分类选择对话框',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章分类选择对话框',
     *     'param'  => ''
     * )
     */
    public function select()
    {
        $ids                 = $this->request->param('ids');
        $selectedIds         = explode(',', $ids);
        $portalCategoryModel = new PortalCategoryModel();
        //<td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
        $tpl = <<<tpl
<tr id='node-\$id' \$parent_id_node style='\$style' data-parent_id='\$parent_id' data-id='\$id'>
    <td style='padding-left:20px;'>\$spacer <span class='\$class'>\$name(\$id)</span></td>
    <td>\$year</td>
    <td>\$description</td>
     <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
               value='\$id' data-name='\$name' \$checked>
    </td>
</tr>
tpl;
        $categoryTree = $portalCategoryModel->adminCategoryTableTree($selectedIds, $tpl);
        $where      = ['delete_time' => 0];
        $categories = $portalCategoryModel->where($where)->select();

        $this->assign('categories', $categories);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('categories_tree', $categoryTree);
        return $this->fetch();
    }


    public function select2()
    {
        $ids                 = $this->request->param('ids');
        $selectedIds         = explode(',', $ids);
        $portalCategoryModel = new PortalCategoryModel();
        //<td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
        $tpl = <<<tpl
<tr id='node-\$id' \$parent_id_node style='\$style' data-parent_id='\$parent_id' data-id='\$id'>
    <td style='padding-left:20px;'>\$spacer <span class='\$class'>\$name(\$id)</span></td>
    <td><span>\$year</span></td>
    <td><span>\$description</span></td>
     <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
               value='\$id' data-name='\$name' \$checked>
    </td>
</tr>
tpl;
        $categoryTree = $portalCategoryModel->adminCategoryTableTree($selectedIds, $tpl);
        $where      = ['delete_time' => 0];
        $categories = $portalCategoryModel->where($where)->select();

        $this->assign('categories', $categories);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('categories_tree', $categoryTree);
        return $this->fetch();
    }

    /**
     * 文章分类排序
     * @adminMenu(
     *     'name'   => '文章分类排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章分类排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('portal_category'));
        $this->success("排序更新成功！", '');
    }

    /**
     * 文章分类显示隐藏
     * @adminMenu(
     *     'name'   => '文章分类显示隐藏',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章分类显示隐藏',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $data                = $this->request->param();
        $portalCategoryModel = new PortalCategoryModel();

        if (isset($data['ids']) && !empty($data["display"])) {
            $ids = $this->request->param('ids/a');
            $portalCategoryModel->where(['id' => ['in', $ids]])->update(['status' => 1]);
            $this->success("更新成功！");
        }

        if (isset($data['ids']) && !empty($data["hide"])) {
            $ids = $this->request->param('ids/a');
            $portalCategoryModel->where(['id' => ['in', $ids]])->update(['status' => 0]);
            $this->success("更新成功！");
        }

    }

    /**
     * 删除文章分类
     * @adminMenu(
     *     'name'   => '删除文章分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除文章分类',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $portalCategoryModel = new PortalCategoryModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findCategory = $portalCategoryModel->where('id', $id)->find();

        if (empty($findCategory)) {
            $this->error('分类不存在!');
        }
//判断此分类有无子分类（不算被删除的子分类）
        $categoryChildrenCount = $portalCategoryModel->where(['parent_id' => $id,'delete_time' => 0])->count();

        if ($categoryChildrenCount > 0) {
            $this->error('此分类有子类无法删除!');
        }

        $categoryPostCount = Db::name('portal_category_post')->where('category_id', $id)->count();

        if ($categoryPostCount > 0) {
            $this->error('此分类有文章无法删除!');
        }

        $data   = [
            'object_id'   => $findCategory['id'],
            'create_time' => time(),
            'table_name'  => 'portal_category',
            'name'        => $findCategory['name']
        ];
        $result = $portalCategoryModel
            ->where('id', $id)
            ->update(['delete_time' => time()]);
        if ($result) {
            Db::name('recycleBin')->insert($data);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
}
