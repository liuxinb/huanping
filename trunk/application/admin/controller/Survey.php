<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\FirmsignSurvey;
use think\Db;
use think\Request;

/**
 * 企业问卷调查管理控制器
 */
class Survey extends Adminbase {

    //model
    private $FirmsignSurvey;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->FirmsignSurvey =new FirmsignSurvey();

    }
    //企业问卷调查列表
    public function index(){
        //  查询企业问卷调查列表所有数据
        $data=$this->FirmsignSurvey->selectFirmsignSurveyData();
        return view('',['all'=>$data,'render'=>$data]);
    }
    
    //删除企业调查问卷题目
    public function del($id){
        $map=['id'=>$id];
        $res=$this->FirmsignSurvey->deleteFirmsignSurveyData($map);
        if($res){
            return $this->success("删除成功","index");
        }else{
            return $this->error("删除失败","index");
        }
    }
    
//    添加企业调查问卷题目
    public function add(){
         if(Request::instance()->isPost()){
            $data=input('post.');
            $data['create_time']=time();
            $res=$this->FirmsignSurvey->insertFirmsignSurveyData($data);
            if($res){
                $this->success('添加成功','index');
            }else{
                $this->error('添加失败','index');
            }
        }
    }
    
        //修改单选题信息
    public function edit(){
        if(Request::instance()->isPost()){
           $data=input('post.');
           $res= $this->FirmsignSurvey->updateFirmsignSurveyData($data);
           if($res){
               $this->success('修改成功','index');
           }else{
               $this->error('修改失败','index');
           }
        }
    }
    
    
}