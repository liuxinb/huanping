<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\User;
use app\common\model\FlvCategory;
use app\common\model\UserPlan;
use app\common\model\Train as TrainModel;
use think\Request;
/**
 * 集中培训计划控制器
 */
class Train extends AdminBase
{

    //model
    private $Train;
    private $userModel;
    private $flvcategoryModel;
    private $userplanModel;


    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Train = new TrainModel();
        $this->userModel = new User();
        $this->flvcategoryModel = new FlvCategory();
        $this->userplanModel = new UserPlan();
    }

    /**
     * 培训计划列表
     */
    public function index(){
        $data=$this->Train->pagTrainData();
       return view('',['all'=>$data]);
    }

    /**
     * 获取学员列表
     * @param Request $request
     * @return \think\response\View
     */
    public function studyList(Request $request)
    {
        //请求的数据
        $trainId =$request->param('id');
//        $map = array();
//        //获取查询条件
//        if(isset($mapName['name'])){
//            $map['aud.name']  = array('like', '%'.input('name').'%');
//        }
        //根据集中培训id查询集中培训学员
        $uId = $this->userplanModel->getUserId($trainId);
        $mapName =$request->param(); //name
//        if(empty($mapName)){
//            return view('',['all' => '']);
//        }
        //获取数据  uid=1,2,3
        $studyResult = $this->userModel->adminXueGetStudyData($mapName,$uId);
        //获取进度
        $studyplanResult = $this->userModel->getStudyPlan($studyResult);
        //分页
        $pageResult = $studyResult->render();
        //返回
        return view('',['all' => $studyplanResult, 'list' => $pageResult,'cid'=>$trainId]);
    }


}