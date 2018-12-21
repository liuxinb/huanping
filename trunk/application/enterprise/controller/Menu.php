<?php

namespace app\enterprise\controller;

use app\common\controller\EnterBase;
use app\common\model\Record as RecordModel;
use app\common\model\CollegeRecord;
use app\common\model\UserPlan;
use app\common\model\User;
use app\common\model\Exam;

class Menu extends EnterBase
{
    private $adminRole;
    private $userplanModel;
    private $userModel;
    private $ExamModel;

    public function __construct()
    {
        $this->userplanModel = new UserPlan();
        $this->userModel = new User();
        $this->ExamModel = new Exam();
        $this->adminRole = parent::role();
        $this->Collegerecord = new Collegerecord();
    }

    /***
     * 菜单页
     * @return \think\response\View
     */
    public function index()
    {
        $firmsignId = session('rootId');
        if ($firmsignId == null) {
            $this->redirect("/");
        }
        //档案提交记录
        $enterpriseId = session('rootId');
        $record = new RecordModel();//企业
        $recordfrequency = $record->recordFrequency($enterpriseId);
        //根据角色发布地址
        if ($this->adminRole == 1) {
            //企业查询登陆者信息
            $recordinfo = $record->recordInfo($enterpriseId,'firmname');
            $recordAddress = '/record';
        } else {
            //院校查询登陆者信息
            $recordinfo = $this->Collegerecord->selectfind($enterpriseId,'academy_name');
            $recordfrequency = 1;
            $recordAddress = '/recordAcademy';
        }
        return view('menu/index',
            [
                'recordResult' =>$recordfrequency,
                'recordinfo' => $recordinfo,
                'recordAddress' => $recordAddress,
                'adminRole' => $this->adminRole
            ]);
    }

    /***
     * 企业管理中心页
     * @return \think\response\View
     */
    public function show()
    {
        $enterpriseId = session('rootId');
        $dataResult = $this->userModel->getStudyData([], $enterpriseId);
        $number = $this->ExamModel->getcertificate($dataResult);
        $numberArray = [];
        $numberArray['oNumber'] = 0;
        $oNumber = 0;
        foreach ($number as $k => $v) {
            if ($v['certificate']) {
                $numberArray['oNumber'] = ++$oNumber;
            } else {
                $numberArray['oNumber'] = $oNumber;
            }
        }
        $count = $this->userModel->getUserplanCount($enterpriseId);
        $numberArray['nNumber'] = $count - $oNumber;
        return view('menu/show',['number' => $numberArray]);
    }

    //系名 报名人数 学完人数统计 ajax请求
    public function academyJson()
    {
        $adminId = session('rootId');
        $dataAll = $this->Collegerecord->statistical($adminId);
        return json($dataAll);
    }

    //学员统计 ajax请求
    public function studentsJson()
    {
        $adminId = session('rootId');
        $dataCount = $this->userModel->collegeShow($adminId);
        return json($dataCount);
    }

    /***
     * 院校管理中心
     */
    public function academy()
    {
        if ($this->adminRole == 1) {
            return $this->redirect('/qiyes');
        }
        return view();
    }

    public function academyExport()
    {
        $adminId = session('rootId');
        //获取数据
        $object = $this->Collegerecord->statistical($adminId);
        //准备导出
        $output = '"院系名","报名人数","学完人数"';
        if ($object) {
            foreach ($object as $value) {
                $output .= "\r\n" . '"' . $value['dataName'] . '","' . $value['dataAll'] . '","'. $value['payAll']. '",';
            }
        }
        //  把 UTF-8  编码字串转换成 GBK 编码字串
        $output = mb_convert_encoding($output, 'GBK', 'UTF-8');
        downFile($output, 'order_' . time() . '.csv');
        exit;
    }
}