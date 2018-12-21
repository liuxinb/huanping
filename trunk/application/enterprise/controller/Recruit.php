<?php

namespace app\enterprise\controller;

use app\common\controller\EnterBase;
use app\common\model\Recruitment;
use app\common\model\Record;
use think\Request;
use \think\Validate;


class Recruit extends EnterBase
{
    private $recruitment;

    public function __construct()
    {
        $this->recruitment =  new Recruitment();
        $this->adminRole = parent::role();
        $this->record = new Record();
    }

    public function index()
    {
        if ($this->adminRole == 2) {
            return $this->redirect('/qiyes');
        }
        $adminId = session('rootId');
        $dataAll = $this->recruitment->dataAll($adminId);
        $recordResult = $this->record->recordFrequency($adminId);
        $page = $dataAll->render();
        return view('',
            [
                'recordCount' => $recordResult,
                'dataAll' => $dataAll,
                'page' => $page
            ]);
    }

    //保存
    public function save(Request $request)
    {
        $adminId = session('rootId');
        $recordResult = $this->record->recordFrequency($adminId);
        if ($recordResult <= 0) {
            return $this->redirect('recruit','请填写档案，才可以发布招聘信息!');
        }
        $dataArray = $request->param();
        $companyName = $this->record->recordInfo($adminId,'firmname');
        $email = $this->record->recordInfo($adminId,'email');
        $emailSize = '请把简历投递至||'.$email->email.'||邮箱。(注:请上传水平能力评价报告)';
        $dataAll = [
            'company_name' => $companyName->firmname,
            'work' => $dataArray['work'],
            'wage' => $dataArray['wage'],
            'size' => $dataArray['size'],
            'degree' => $dataArray['degree'],
            'experience' => $dataArray['experience'],
            'work_describe' =>  str_replace("\r\n","&amp",$dataArray['work_describe']),
            'company_size' => str_replace("\r\n","",$dataArray['company_size']),
            'email_size' => $emailSize,
            'allow_date' => time(),
            'admin_id' => $adminId,
            'id' => $dataArray['id']
        ];
        $result = $this->recruitment->dataSave($dataAll);
        return $result ? true : false;

    }

    //查看
    public function show(Request $request)
    {
        $id = $request->param('id');
        $dataAll = $this->recruitment->find($id);
        $dataAll->work_describe = str_replace("&amp","\r\n",$dataAll->work_describe);
        $dataAll->email_size = str_replace("||","",$dataAll->email_size);
        return json($dataAll);
    }

    //状态
    public function state(Request $request)
    {
        $data = $request->param();
        $dataArray = [
            'id' => $data['id'],
            'state' => $data['state'],
        ];
        $result = $this->recruitment->dataUpdate($dataArray);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    //删除
    public function delete(Request $request)
    {
        $id = $request->param('id');
        $result = $this->recruitment->dataDelete($id);
        return $result ? true : false;
    }
}