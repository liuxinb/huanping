<?php

namespace app\enterprise\controller;

use app\common\model\User;
use think\Request;
use app\common\model\Exam;
use app\common\controller\EnterBase;


class Student extends EnterBase
{
    private $userModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->userModel = new User();
        $this->ExamModel = new Exam();
    }

    const FROZEN = '-1';
    const MORMAL = '1';

    /**
     * 展示学习管理学员
     * @param Request $request
     * @return \think\response\View
     */
    public function index(Request $request)
    {
        //获取ID
        $enterpriseId = session('rootId');
        $type = \session('adminRole')->type;
        $this->assign("role",$type);
        //查询订单
        $enterpriseOrder = $this->userModel->enterpriseOrder($enterpriseId);
        if (!$enterpriseOrder) {
            return view('student/index', ['data' => []]);
        }
        //请求
        $requestData = $request->except(['page']);
        //获取数据
        $dataResult = $this->userModel->getStudyData($requestData, $enterpriseId);
        //证书
        $studyResult = $this->ExamModel->getcertificate($dataResult);
        //获取进度
        $studyplanResult = $this->userModel->getStudyPlan($studyResult);
        //分页
        $pageResult = $studyResult->render();
        //返回
        return view('student/index', ['data' => $studyplanResult, 'list' => $pageResult]);
    }
}