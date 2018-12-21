<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\Evaluation;
use think\Request;


/**
 *
 * 评论管理
 */
class Evaluate extends AdminBase
{
    //model
    private $Evaluation;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Evaluation =new Evaluation();
    }

    public function index(){
        $data= $this->Evaluation->ShowEvaluateData();
        return view('',['all'=>$data]);
    }

    public function del($id){
        $map=['id'=>$id];
        $data=$this->Evaluation->delEvaluateData($map);
        if($data){
            $this->success('删除成功','index');
        }else{
            $this->error('删除失败','index');
        }
    }

    //批量删除
    public function batchDelete($ids){
        $result = $this->Evaluation->destroy($ids);
        if($result){
            return json(["success"=>"删除成功","ids"=>$result]);
        }else{
            return json(["error"=>"删除失败","ids"=>$result]);
        }

    }
}
