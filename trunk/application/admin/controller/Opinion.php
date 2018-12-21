<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\Opinion as OpinionModel;
use app\common\model\Base;
use think\Request;

/**
 * App意见反馈控制器
 */
class Opinion extends Adminbase {
    //model
    private $OpinionModel;
//    private $Base;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->OpinionModel =new OpinionModel();
//        $this->Base =new Base();
    }
//    意见反馈列表
    public function index(){
        //查询Opinion列表和USER_DETAIL名称
        $data=  $this->OpinionModel->selectOpinionData();
        return view('',['all'=>$data]);
    }

    //反馈列表删除
    public function del($id){
        //根据条件删除Opinion数据
        $map=array('id'=>$id);
        $res= $this->OpinionModel->delOpinionData($map);
        if($res){
            return $this->success('删除成功','index');
        }else{
            return $this->error('删除失败','index');
        }
    }

    //批量删除
    public function batchDelete($ids){
        $result = $this->OpinionModel->destroy($ids);
        return json(["success"=>"删除成功","ids"=>$result]);
    }



}


