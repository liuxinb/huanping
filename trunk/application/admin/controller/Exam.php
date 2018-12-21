<?php
namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\Exam as ExamModel;//命名空间
use think\Request;

/**
 * 学员app考试控制器
 */
class Exam extends Adminbase
{
    //model
    private $Exam;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Exam =new ExamModel();
    }

    //企业信息列表
    public function index(){
        /*
         * 查询每个学员考试的成绩
         */
        $data=  Request::instance()->param();
//        //获取查询条件
        $map = array();
        if(isset($data['phone'])){
            $map['u.phone']  = array('like', '%'.input('phone').'%');
        }
        //档案表 员工表 一对一关系
        $data = $this->Exam ->selectExamData($map);
        //根据条件查询结果为空时
//        if(empty($dangan)){
//            return view('',['all'=>$data,'render'=>$dangan]);
//        }
        return view('',['all'=>$data]);
    }

    public function del_exam($id){
        $map=array('id'=>$id);
        $data=$this->Exam->deleteExam($map);
        if($data){
            $this->success("删除成功",'index');
        }else{
            $this->error('删除失败','index');
        }
    }


}

