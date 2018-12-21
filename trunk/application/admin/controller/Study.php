<?php

namespace app\admin\controller;


use app\common\controller\Adminbase;
use app\common\model\User;
use app\common\model\UserPlan;
use think\Request;


class Study extends Adminbase
{
    private $userModel;
    private $UserPlan;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->userModel = new User();
        $this->UserPlan = new UserPlan();
    }

    /**
     * 展示所有学习管理学员
     * @param Request $request
     * @return \think\response\View
     */
    public function index(Request $request)
    {
        //请求的数据
        $data= $request->param();
        $map = array();
        //获取查询条件
        if(isset($data['name'])){
            $map['aud.name']  = array('like', '%'.input('name').'%');
        }
        //查询所有员工进度表的uid  uid=1,2,3
        $uid = $this->UserPlan->adminGetUserId();
        //获取数据
        $studyResult = $this->userModel->adminGetStudyData($map,$uid);
//        dump($studyResult);die;
        //获取进度
        $studyplanResult = $this->userModel->getStudyPlan($studyResult);
        //分页
        $pageResult = $studyResult->render();
        //返回
        return view('', ['all' => $studyplanResult,'list'=>$pageResult]);

    }
}