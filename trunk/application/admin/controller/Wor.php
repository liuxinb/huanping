<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\TestBase;
use app\common\model\FlvMovie;
use app\common\model\FlvCategory;
use think\Request;
use think\Validate;



/**
 * 题库管理控制器
 */
class Wor extends Adminbase {

    //model
    private $TestBase;
    private $FlvMovie;
    private $FlvCategory;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->TestBase = new TestBase();
        $this->FlvMovie = new FlvMovie();
        $this->FlvCategory = new FlvCategory();

    }

    //题库管理列表  判断题管理
    public function index(){
        //  接收表单值
        $data=  Request::instance()->param();
        $map = array();
        //获取查询条件
        if(isset($data['content'])){
            $map['b.content']  = array('like', '%'.input('content').'%');
        }
         $map['b.type']=3;
        //  查询状态为0 判断题
        $dataBinary=$this->TestBase->selectTestBaseBinaryData($map);
        foreach ($dataBinary as $k=>$v){
            $dmovie=$v->movie_id;
            $dataBinary= substr($dmovie,3);
        }
        $dataBinary=$this->TestBase->selectTestBaseBinaryData($map);
        //获取所有视频的id,title
        $movie_title=$this->FlvMovie->movie_title_pub();
        //获取所有视频包的id,title
        $cate_title=$this->FlvCategory->getCategoryData();
        //合并视频和视频包
//        $merge=array_merge($movie_title,$cate_title);
        //渲染 模板 分页 视频id,title
        return view('',['binary'=>$dataBinary,'renderBinary'=>$dataBinary,'movie_title'=>$movie_title,'cate_title'=>$cate_title]);
    }

    //单选题列表
    public function single_index(){
        //  接收表单值
        $data=  Request::instance()->param();
        $map = array();
        //获取查询条件
        if(isset($data['content'])){
            $map['b.content']  = array('like', '%'.input('content').'%');
        }
        $map['b.type']=1;
         //  查询状态为1 单选题 分页
        $dataSingle=$this->TestBase->selectTestBaseSingleMultipleData($map);
        //获取数据 循环组合
        $datas=$this->TestBase->selectTestBaseSingleMultipleDataPub($dataSingle);
        //获取所有flv_movie视频的id,title
        $movie_title=$this->FlvMovie->movie_title_pub();
        //获取所有视频包的id,title
        $cate_title=$this->FlvCategory->getCategoryData();
        //合并视频和视频包
//        $merge=array_merge($movie_title,$cate_title);
        //渲染 模板 分页 视频id,title
        return view('',['single'=>$datas,'renderSingle'=>$dataSingle,'movie_title'=>$movie_title,'cate_title'=>$cate_title]);
    }
    
    //多选题列表
    public function multiple_index(){
        //  接收表单值
        $data=  Request::instance()->param();
        $map = array();
        //获取查询条件
        if(isset($data['content'])){
            $map['b.content']  = array('like', '%'.input('content').'%');
        }
        $map['b.type']=2;
        //  查询状态为2 多选题
        $dataMultiple=$this->TestBase->selectTestBaseSingleMultipleData($map);
        //获取数据 循环组合
        $datas=$this->TestBase->selectTestBaseSingleMultipleDataPub($dataMultiple);
        //获取所有视频的id,title
        $movie_title=$this->FlvMovie->movie_title_pub();
        //获取所有视频包的id,title
        $cate_title=$this->FlvCategory->getCategoryData();
        //渲染 模板 分页 视频id,title
        return view('',['multiple'=>$datas,'renderMultiple'=>$dataMultiple,'movie_title'=>$movie_title,'cate_title'=>$cate_title]);
    }

    //组合数据公共方法
    public function pub($data){
        $questent=[];
        foreach ($data as $key=>$v) {
            $questent[$key]=[
                 'id' => $v->id,      
                'movie_id' => $v->movie_id?$v->movie_id:'',
                'content' => $v->content,
                'answer' => $v->answer,
                'A' => $v->A,
                'B' => $v->B,
                'C' => $v->C,
                'D' => $v->D,
                'title' => $v->title,
                'create_time' => $v->create_time,
            ];
        }
        return $questent;
    }


    //判断题修改
    public function edit($id){
        if(Request::instance()->isPost()){
            //修改答案接收答案公用方法
            $data=$this->TestBase->editPub();
            $map=['id'=>$id];
            $res=$this->TestBase->updateTestBaseData($map,$data);
            if($res){
               $this->success('修改成功','index');
            }else{
                $this->error('修改失败','index');
            }
        }
    }

    //判断题删除
    public function del($id){
        $map=['id'=>$id];
        //根据id删除判断题
       $res= $this->TestBase->deleteTestBaseData($map);
       if($res){
           $this->success('删除成功','index');
       }else{
           $this->error('删除失败','index');
       }
    }
    
    //添加判断题
    public function add(){
        if(Request::instance()->isPost()){
            //获取表单数据 处理answer及其他 插入数据
            $res=$this->TestBase->addBinaryTestBaseData();
            if($res){
                $this->success('添加成功','index');
            }else{
                $this->error('添加失败','index');
            }
        }
    }
    
    //修改单选题信息
    public function edit_single(){
        if(Request::instance()->isPost()){
            //获取表单单选题数据 并组合数据
            $data=$this->TestBase->getSingleData();
            //更新单选题数据
           $res=$this->TestBase->updateSingleTestBaseData($data);
           if($res){
               $this->success('修改成功','single_index');
           }else{
               $this->error('修改失败','single_index');
           }
        }
    }
    
    //添加单选题信息
    public function add_single(){
        if(Request::instance()->isPost()){
            //获取表单数据 添加单选题信息
            $res=$this->TestBase->addSingleData();
            if($res){
                $this->success('添加成功','single_index');
            }else{
                $this->error('添加失败','single_index');
            }
        }
    }
    
    //删除单选题
    public function del_single($id){
        $map=['id'=>$id];
       $res=$this->TestBase-> deleteTestBaseData($map);
       if($res){
           $this->success('删除成功','single_index');
       }else{
           $this->error('删除失败','single_index');
       }
    }
    
    //修改多选题
    public function edit_mult(){
        if(Request::instance()->isPost()){
            //获取表单数据 组合数据
            $data=$this->TestBase->editMultData();
             //判断多选框为空的情况
            if(isset($data['answer'])){
                $data['answer']=implode(",",$data['answer']);
            }else{
                $this->error('未选中选项','multiple_index');
            }
            //更新数据
            $res=$this->TestBase->updateSingleTestBaseData($data);
            if($res){
                $this->success('修改成功','multiple_index');
            }else{
                $this->error('修改失败','multiple_index');
            }
        }
    }
      //添加多选题
    public function add_mult(){
        if(Request::instance()->isPost()){
            //获取表单数据 处理多选题条件
            $data=$this->TestBase->addMultData();
            //判断多选框为空的情况
            if(isset($data['answer'])){
                $data['answer']=implode(",",$data['answer']);
            }else{
                $this->error('未选中选项','multiple_index');
            }
            //插入多选题数据
            $res=$this->TestBase->insertAddMultData($data);
            if($res){
                $this->success('添加成功','multiple_index');
            }else{
                $this->error('添加失败','multiple_index');
            }
        }
    }


    //删除多选题
    public function del_mult($id){
        $map=['id'=>$id];
        //删除多选题
       $res=$this->TestBase->deleteTestBaseData($map);
       if($res){
           $this->success('删除成功','multiple_index');
       }else{
           $this->error('删除失败','multiple_index');
       }
    }


    //批量删除
    public function batchDelete($ids){
        $result = $this->TestBase->destroy($ids);
//        dump($result);die;
        return json(["success"=>"删除成功","id"=>$result]);

    }

    /**
     * 批量导入视图
     * @return \think\response\View
     */
    public function batchIndex()
    {
        return view('wor/lead');
    }

    protected $msg = [
        'movie_id.require' => '所属视频不能为空',
        'content.require' => '题干不能为空',
        'answer.require' => '答案不能为空',
        'type.require' => '类型不能为空',
    ];

    protected $validate = [
        'movie_id' => 'require',
        'content' => 'require',
        'answer' => 'require',
        'type' => 'require',
    ];

    //批量上传试题
    public function academyBatch(Request $request)
    {
        //获取ID
        $enterpriseId = session('rootId');
        //判断文件夹是否存在不存在则创建
        if (!file_exists(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId)) {
            mkdir(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId, 0777, true);
        }
        //处理Excel文件请求
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $file = request()->file('file');
        if (is_null($file)) {
            return $this->success("上传文件不能为空~", '/admin/wor/index');
        }
        $info = $file->validate(['size' => 512000, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId);
        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId . DS . $exclePath;   //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
            echo "<pre>";
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            array_shift($excel_array);  //删除第一个数组(标题);
            $sumCount = 0;
            $data = [];
            $arrData=[];
            foreach ($excel_array as $k => $v) {
                $data[$k]['movie_id'] = trim($v[0]); //所属视频
                $data[$k]['content'] = trim($v[1]); //题干
                $arr = explode(' ',trim($v[2]));
                foreach ($arr as  $key=>$vo){
                    $arrData[$key] =substr($vo,3);
                }
                $data[$k]['A'] = trim($arrData[0]); //备选答案
                $data[$k]['B'] = trim($arrData[1]); //备选答案
                $data[$k]['C'] = trim($arrData[2])?trim($arrData[2]):''; //备选答案
                $data[$k]['D'] = trim($arrData[3])?trim($arrData[3]):''; //备选答案

                $data[$k]['answer'] = trim($v[3]); //答案
                $data[$k]['type'] = trim($v[4]);//题型
                $sumCount++;
            }

            //删除空数据
            foreach ($data as $k => $v) {
                if (preg_match("/\s/", $v['movie_id'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['content'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['A'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['B'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['C'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['D'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['answer'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['type'])) {
                    unset($data[$k]);
                }
            }
//            //验证导入的数据真实性
            $newArray = $this->academyverificationData($data);
            if (!$newArray) {
                return $this->success("请检查数据有效性,重新上传~", '/admin/wor/batchIndex');
            }

            //判断所属视频是否存在
            $q = 1;
            foreach ($newArray as $k => $v) {
                $q++;
                $subjectId = $this->FlvCategory->categoryid($v['movie_id']);
//                dd($subjectId);
                if (!$subjectId) {
                    return $this->success('导入失败:第' . $q . '行,所属视频不存在', '/admin/wor/batchIndex');
                } else {
                    $newArray[$k]['movie_id'] = $subjectId;
                }
            }

            //拼接成数组，插入数据
            $user = [];
            $i = 0;
            foreach ($newArray as $k => $v) {
                if($v['movie_id']==1){
                    $user[$k]['movie_id'] = 'cid'.$v['movie_id'];
                }else{
                    $user[$k]['movie_id'] = $v['movie_id'];
                }
                if($v['type']=="判断题"){
                    $user[$k]['type'] = '3';
                    $user[$k]['A'] = '对';
                    $user[$k]['B'] = '错';
                    $user[$k]['C'] = '';
                    $user[$k]['D'] = '';
                    $res=[
                        'A' => '对',
                        'B' => '错',
                        'C' => '',
                        'D' => '',
                    ];
                    $user[$k]['option'] = json_encode($res);
                    if($v['answer']=='A'){
                        $user[$k]['answer'] = 1;
                    }else{
                        $user[$k]['answer'] = 0;
                    }
                }elseif ($v['type']=="单选题"){
                    $user[$k]['type'] = '1';
                    $user[$k]['A'] = $v['A'];
                    $user[$k]['B'] = $v['B'];
                    $user[$k]['C'] = $v['C'];
                    $user[$k]['D'] = $v['D'];
                    $res=[
                        'A' => $v['A'],
                        'B' => $v['B'],
                        'C' => $v['C'],
                        'D' => $v['D'],
                    ];
                    $user[$k]['option'] = json_encode($res);
                    if($v['answer']=='A'){
                        $user[$k]['answer'] = 0;
                    }elseif ($v['answer']=='B'){
                        $user[$k]['answer'] = 1;
                    }elseif ($v['answer']=='C'){
                        $user[$k]['answer'] = 2;
                    }else{
                        $user[$k]['answer'] = 3;
                    }
                }elseif($v['type']=="多选题"){
                    $user[$k]['type'] = '2';
                    $user[$k]['A'] = $v['A'];
                    $user[$k]['B'] = $v['B'];
                    $user[$k]['C'] = $v['C'];
                    $user[$k]['D'] = $v['D'];
                    $res=[
                        'A' => $v['A'],
                        'B' => $v['B'],
                        'C' => $v['C'],
                        'D' => $v['D'],
                    ];
                    $user[$k]['option'] = json_encode($res);
                    $user[$k]['answer']=$this->TestBase->answerPubData($v['answer']);
                }
                $user[$k]['content'] = $v['content'];
                $user[$k]['create_time'] = time(); //导入时间
                $i++;
            }

            //数据添加操作
            $s = 0;
            foreach ($user as $key => $item) {
                $success = $this->TestBase->create($item);
                $s++;
            }
            //响应
            return $this->success("总{$sumCount}条，成功{$s}条", '/admin/wor/index');
        } else {
            return $this->success($file->getError(), '/admin/wor/batchIndex');
        }
    }

    //验证格式
    public function academyverificationData($paramArray)
    {
        $ii = 1;
        foreach ($paramArray as $key => $array) {
            $ii++;
            //验证数据是否为空
            if (empty($array['movie_id']) || empty($array['content']) || empty($array['answer']) || empty($array['type'])) {
                return $this->success('导入失败:第' . $ii . '行，数据不能为空', '/admin/wor/batchIndex');
            }
            $preg_name = '/^[a-dA-D,]{1,7}$/';
            if (!preg_match($preg_name, $array['answer'])) {
                return $this->success('导入失败:第' . $ii . '行，答案格式不正确', '/admin/wor/batchIndex');
            }
//            $preg_name = '/^[\u4e00-\u9fa5]*$/';
//            if (!preg_match($preg_name, $array['type'])) {
//                return $this->success('导入失败:第' . $ii . '行，题型格式不正确', '/admin/wor/batchIndex');
//            }
        }
        //验证数据格式
        $validate = new Validate($this->validate, $this->msg);
        $i = 1;
        foreach ($paramArray as $key => $item) {
            $i++;
            if (!$validate->check($item)) {
                return $this->success('导入失败: ' . $i . '行' . $validate->getError(), '/admin/wor/batchIndex');
            }
        }
        return $paramArray;
    }

}

