<?php

namespace app\index\controller;
use think\Controller;
use app\common\model\CollegeRecord;
use app\common\model\User;
use think\Request;

class Academy extends Controller
{
    private $Collegerecord;
    private $User;

    public function __construct()
    {
        $this->Collegerecord = new Collegerecord();
        $this->User = new User();
    }

    //首页
    public function index()
    {
        $adminId = session('audit');
        if (!$adminId) {
            return $this->redirect('/');
        }
        return view('academy/info');
    }

    //保存
    public function saveName(Request $request)
    {
        $adminId = session('audit');
        $dataArray = $request->param();
        //院校
        $academy = [
            'academy_name' => $dataArray['academy_name'],
            'organization_code' => $dataArray['organization_code'],
            'pid' => '0',
            'admin_id' => $adminId,
            'create_time' => time()
        ];
        $academy = $this->Collegerecord->dataCreate($academy);
        //院系
        $subjects = [];
        foreach ($dataArray['subjects'] as $k=>$item)
        {
            $subjects[$k]['academy_name'] = $dataArray['subjects'][$k];
            $subjects[$k]['pid'] = $academy->id;
            $subjects[$k]['admin_id'] = $adminId;
            $subjects[$k]['create_time'] = time();
        }

        $result = $this->Collegerecord->dataSaveAll($subjects);
        \session('audit',NULL);
        return $result ? true : false;
    }

}