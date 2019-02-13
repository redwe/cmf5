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

class ExamController extends AdminBaseController
{

    public function index(){
        $data1=Db::name('kscate')->where((['parentid'=>0]))->select();
        $html = '';
        if($data1) {
            foreach ($data1 as $key1 => $val1) {
                $html .= '<tr data-tt-id="'.$val1['id'].'"><td>' . $val1['classname'] . '</td><td>' . $val1['aclassname'] . '</td><td>' . $val1['teacher'] . '</td><td><a href="'.url('exam/addType',array('id'=>$val1['id'],'level'=>1)).'"">添加子分类</a>  <a href="'.url('exam/edit',array('id'=>$val1['id'],'name'=>$val1['classname'])).'">编辑</a>  <a  href="'.url('exam/del',array('id'=>$val1['id'])).'">删除</a></td></tr>';
                $data2 = Db::name('kscate')->where((['parentid'=>$val1['id']]))->select();
                if($data2){
                    foreach ($data2 as $key2=>$val2){
                        $html .= '<tr data-tt-id="'.$val2['id'].'" data-tt-parent-id="'.$val2['parentid'].'"><td>' . $val2['classname'] . '</td><td>' . $val2['aclassname'] . '</td><td>' . $val2['teacher'] . '</td><td><a href="'.url('exam/addType',array('id'=>$val2['id'],'level'=>2)).'"">添加子分类</a> <a href="'.url('exam/edit',array('id'=>$val2['id'],'name'=>$val2['classname'])).'">编辑</a>  <a  href="'.url('exam/del',array('id'=>$val2['id'])).'">删除</a></td></tr>';
                        $data3 =  Db::name('kscate')->where((['parentid'=>$val2['id']]))->select();
                        if($data3){
                            foreach ($data3 as $key3=>$val3) {
                                $html .= '<tr data-tt-id="'.$val3['id'].'" data-tt-parent-id="'.$val3['parentid'].'"><td>' . $val3['classname'] . '</td><td>' . $val3['aclassname'] . '</td><td>' . $val3['teacher'] . '</td><td><a href="'.url('exam/addType',array('id'=>$val3['id'],'level'=>3)).'"">添加试卷</a>  <a href="'.url('exam/edit',array('id'=>$val3['id'],'name'=>$val3['classname'])).'">编辑</a> <a  href="'.url('exam/del',array('id'=>$val3['id'])).'">删除</a></td></tr>';
                                $data4 =  Db::name('kscate')->where((['parentid'=>$val3['id']]))->select();
                                if($data4){
                                    foreach ($data4 as $key4=>$val4) {
                                        $count = Db::name('ksti')->where(['fenlei' => $val4['id']])->count();
                                        $importurl = url("exam/addType",array("id"=>$val4["id"]));
                                        $cateurl = url("exam/examlist",array("cateid"=>$val4['id']));
                                        $html .= '<tr data-tt-id="' . $val4['id'] . '" data-tt-parent-id="' . $val4['parentid'] . '">
                 <td><a href="'.$cateurl.'">' . $val4['classname'] . '(试卷号' . $val4['id'] . ')' . '(时长' . $val4['timelength'] . ')' . '(试题数' . $count . ')' . '</td></a><td>' . $val4['aclassname'] . '</td><td>' . $val4['teacher'] . '</td><td><a href="' . url('exam/edit', array('id' => $val4['id'], 'name' => $val4['classname'], 'timelength' => $val4['timelength'])) . '">编辑</a>  <a  href="' . url('exam/del', array('id' => $val4['id'])) . '">删除</a>  <a  href="' . url('exam/addExam', array('id' => $val4['id'])) . '">添加题目</a> <a  href="'.url("exam/importexam").'">导入课时</a> <a  href="'.$importurl.'">添加章节</a></td></tr>';
                                        $data5 = Db::name('kscate')->where((['parentid' => $val4['id']]))->select();
                                        if ($data5) {
                                            foreach ($data5 as $key5 => $val5) {
                                                $count = Db::name('ksti')->where(['fenlei' => $val5['id']])->count();
                                                $examurl = url('Exam/examlist',array('cateid'=>$val5['id']));
                                                $html .= '<tr data-tt-id="' . $val5['id'] . '" data-tt-parent-id="' . $val5['parentid'] . '">
                 <td><a href="'.$examurl.'">' . $val5['classname'] . '(试卷号' . $val5['id'] . ')' . '(时长' . $val5['timelength'] . ')' . '(试题数' . $count . ')' .
                                                    '</td></a><td>' . $val5['aclassname'] . '</td><td>' . $val5['teacher'] . '</td><td><a href="' . url('Kaoshi/edit', array('id' => $val5['id'], 'name' => $val5['classname'], 'timelength' => $val5['timelength'])) . '">编辑</a>  <a  href="' . url('Kaoshi/del', array('id' => $val5['id'])) . '">删除</a>  <a  href="' . url('Exam/addExam', array('id' => $val5['id'])) . '">添加题目</a> <a  href="'.url("exam/importexam").'">导入章节</a> </td></tr>';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }
        }
        $this->assign('html',$html);
        return $this->fetch();
    }

    //试题列表
    public function examlist()
    {
        $cateId = $this->request->param('cateid');
        $condition = $cateId?['fenlei'=>$cateId]:'';
        $condition['status']=['neq',0];
        $parents = Db::name('kscate')->where(['id'=>$cateId])->field('parentid')->find();
        $parentid = $parents['parentid'];
        $class = Db::name('kscate')->where(['id'=>$parentid])->field('classname')->find();
        $classname = $class['classname'];
        if($classname=='章节练习'){
            $url = url("exam/addType",array('id'=>$cateId,"level"=>6));
            $this->assign('flag','章节练习');
            $this->assign('url',$url);
            //章节练习列表
            $zjlist=Db::name('kscate')->where(['parentid'=>$cateId])->select();
            $this->assign('zjlist',$zjlist);
        }
        $field = 'k.*,c.classname,m.title';
        $join = [
            ["kscate c","k.fenlei=c.id"],
            ["kstimu m","k.tixing=m.id"]
        ];
        $exam_questions = Db::name('ksti')->alias("k")
            ->where($condition)
            ->join($join)
            ->field($field)
            ->order('k.sort asc')
            ->paginate(20);
        //dump($exam_questions);
        $this->assign('cateid',$cateId);
        $this->assign('exam_questions',$exam_questions);
        $this->assign('page',$exam_questions->render());// 赋值分页输出
        return $this->fetch();
    }

    public function addtype(){
        $id = $this->request->param('id');
        if($id == 0){
            $data['parentstr'] = '';
            $data['classname'] = '顶级分类';

        }else {
            $data = Db::name('kscate')->where(['id'=>$id])->find();
        }
        /*if(empty($data)){
            $this->error('上级分类错误');
        }*/
        //提交分类
        if($this->request->isPost()){
            $classname = $this->request->param('classname');
            $aclassname = $this->request->param('aclassname');
            $teacher = $this->request->param('teacher');
            $level=$_GET['level'];
            $timelength=$this->request->param('timelength');
            if(empty($classname)){
                $this->error('分类名称不能为空');
            }
            $save_data = array(
                'parentid'=>$id,
                'parentstr'=>$data['parentstr'].$id.',',
                'classname'=>$classname,
                'aclassname'=>$aclassname,
                'checkinfo'=>'true',
                'orderid'=>1,
                'teacher'=>$teacher,
                'level'=>$level,
                'timelength'=>$timelength
            );
            if(Db::name('kscate')->insert($save_data)){
                $this->success('添加成功',url('exam/index'));
            }else{
                $this->error('添加失败');
            }
            exit();
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function addexam(){
        $id=$this->request->param('id');
        if($id>0){
            $arr=Db::name('ksti')->where(['id'=>$id])->select();
            $this->assign('arr',$arr);
        }
        if($this->request->isPost()){
            $data['fenlei']=$_POST['fenlei'];
            $data['tixing']=$_POST['tixing'];
            $data['tigan']=$_POST['tigan'];
            $data['sort']=$_POST['sort'];
            if($_POST['tixing']==1||$_POST['tixing']==2){
                $data['a']=$_POST['abc'];
                $data['b']=$_POST['b'];
                $data['c']=$_POST['c'];
                $data['d']=$_POST['d'];
                $data['e']=$_POST['e'];
                $data['answer']=$_POST['answer'];
                $data['fenzhi']=$_POST['fenzhi'];
                $data['jiexi']=$_POST['jiexi'];
            }
            if($_POST['tixing']==3){
                $_POST['info']=(array_filter($_POST['info'],function($value){
                    if(strlen($value)>0){
                        return $value;
                    };
                }));
                $data['content']=serialize($_POST['info']);
                $data['addtime']=time();
            }
            $user=Db::name('Kaoshi');
            if (!$user){ // 创建数据对象
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error();
            }else{
                // 验证通过 写入新增数据
                if($user->insert($data)){
                    $this->success('添加成功');
                };
            }

        }else{
            return $this->fetch();
        }
    }

    public function edit(){
        if($this->request->isPost()){
            $data['id']=$_POST['id'];
            $data['classname']=$_POST['classname'];
            $data['timelength']=$_POST['timelength'];
            $cate= Db::name('kscate');
            if($cate->update($data)){
                $this->success('编辑成功',url('exam/index'));
            };
        }else{
            $data['id']=$this->request->param('id');
            $data['name']=$this->request->param('name');
            $data['timelength']=$this->request->param('timelength');
            $this->assign('data',$data);
            return $this->fetch();
        }
    }

    //删除考试分类
    public function del(){
        $id=$this->request->param('id');
        $info=Db::name('kscate')->where(['id'=>$id])->delete();
        if($info){
            $this->success('删除成功');
        }
    }

    //编辑试题
    public function editexam(){
        if($this->request->isPost()){
            $data['fenlei']=$_POST['fenlei'];
            $data['tixing']=$_POST['tixing'];
            $data['tigan']=$_POST['tigan'];
            $data['sort']=$_POST['sort'];
            $data['fenzhi']=$_POST['fenzhi'];
            $data['id']=$_POST['id'];
            if($_POST['tixing']==1||$_POST['tixing']==2){
                $data['a']=$_POST['abc'];
                $data['b']=$_POST['b'];
                $data['c']=$_POST['c'];
                $data['d']=$_POST['d'];
                $data['e']=$_POST['e'];
                $data['answer']=$_POST['answer'];
                $data['jiexi']=$_POST['jiexi'];
            }
            if($_POST['tixing']==3){
                $_POST['info']=(array_filter($_POST['info'],function($value){
                    if(strlen($value)>0){
                        return $value;
                    };
                }));
                $data['content']=serialize($_POST['info']);
                $data['addtime']=time();
            }
            $user= Db::name('Kaoshi');
            if (!$user){ // 创建数据对象
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error();
            }else{
                // 验证通过 写入新增数据
                if($user->update($data)){
                    $this->success('修改成功',url('Exam/examlist'));
                };
            }

        }else{
            $id=$this->request->param('id');
            $info=Db::name('ksti')->where(['id'=>$id])->find();
            if($info['content']){
                $info['content']=unserialize($info['content']);
            }
            // p($info);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    //删除试题
    public function delexam()
    {
        $id = $this->request->param('id');
        $res = Db::name('ksti')->where(array('id'=>$id))->delete();

        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }


    //上传试题
    public function importexam()
    {
        if($this->request->isPost()){
            $files = $_FILES['exl'];
            $updata = new Upload();// 实例化上传类
            $file_name = '';
            $rootUrl = $_SERVER['DOCUMENT_ROOT'];

            $file_path = '/upload/excel/';
            $file_array = ['xls','xlsx'];
            //上传文件
            if(request()->file('exl')){
                $uploads = $updata->uploadpic('exl',$file_path,$file_array);
                if ($uploads['res']) {
                    $file_name = $uploads['data'];
                }
            }
            $exl = $this->importShiti($rootUrl.$file_name);
            // unlink($file_name);
            $this->success('成功',url('exam/index'));
        }else{
            return $this->fetch();
        }

    }

//
    public function importShiti($path){
        $data=($this->import_exl($path));
        $i=0;
        foreach($data as $v){
            $v['addtime']=time();
            $res = Db::name('ksti')->insert($v);
            $i = $i + $res;
        }
        return $i;
    }

    /* 处理上传exl数据
     * $file_name  文件路径
     */
    public function import_exl($path){

        //import("Org.Util.PHPExcel", '', '.php'); //PHPExcel没有用命名空间，只能import加载
        //import("Org.Util.PHPExcel.Reader.Excel2007", '', '.php'); //加载读.xlsx的类
        //$objReader=new \PHPExcel_Reader_Excel2007(); //new一下
        //$objPHPExcel = $objReader->load($file_name,$encode='utf-8');
        $objPHPExcel = new \PHPExcel();
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

        $data=array();

        for($i=1;$i<$highestRow+1;$i++){

            $data[]=array(
                'tigan'       			=>trim($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue()),
                'fenlei'			=>trim($objPHPExcel->getActiveSheet()->getCell('B'.$i)->getValue()),
                'tixing'	=>trim($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getValue()),
                'answer'					=>trim($objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue()),
                'fenzhi'			=>trim($objPHPExcel->getActiveSheet()->getCell('E'.$i)->getValue()),
                'jiexi'					=>trim($objPHPExcel->getActiveSheet()->getCell('F'.$i)->getValue()),
                'a'				=>trim($objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue()),
                'b'				=>trim($objPHPExcel->getActiveSheet()->getCell('H'.$i)->getValue()),
                'c'				=>trim($objPHPExcel->getActiveSheet()->getCell('I'.$i)->getValue()),
                'd'				=>trim($objPHPExcel->getActiveSheet()->getCell('J'.$i)->getValue()),
                'e'				=>trim($objPHPExcel->getActiveSheet()->getCell('K'.$i)->getValue()),
                'sort'				=>trim($objPHPExcel->getActiveSheet()->getCell('L'.$i)->getValue()),
                'content'				=>trim($objPHPExcel->getActiveSheet()->getCell('M'.$i)->getValue()),
            );
        }
        return $data;
    }
}
