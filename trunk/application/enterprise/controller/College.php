<?php

namespace app\enterprise\controller;

use app\common\controller\EnterBase;
use app\common\model\CollegeRecord;
use app\common\model\Draw as DrawCommon;
use app\common\model\User;
use think\Request;
use \think\Validate;

class College extends EnterBase
{
    private $Collegerecord;
    private $User;
    private $DrawCommon;

    public function __construct()
    {
        $this->Collegerecord = new Collegerecord();
        $this->User = new User();
        $this->DrawCommon = new DrawCommon();
    }

    //首页
    public function index()
    {
        $adminId = session('rootId');
        $dataAll = $this->Collegerecord->dataSelect($adminId);

        //发票抬头表
        $dataDraw = $this->DrawCommon->BaseFind(['uid'=>$adminId,'type'=>2]);
        if (!$dataDraw){
            $dataDraw['invoice_name'] = "";
            $dataDraw['identification'] = "";
            $dataDraw['phone'] = "";
            $dataDraw['address'] = "";
            $dataDraw['bank'] = "";
            $dataDraw['number'] = "";
        }
        return view('college/index',['dataAll'=>$dataAll,'dataDraw'=>$dataDraw]);
    }

    //院校发票修改
    public function savedraw(Request $request)
    {
        $adminId = session('rootId');
        $type = \session('adminRole')->type;
        $dataArray = $request->param();
        //验证发票名称
        if (!preg_match('/^[\(\)\x{4e00}-\x{9fa5}A-Z_]+$/u', $dataArray['invoice_name'])) {
            return "发票名称不符合规则，只支持中文，大写英文，英文小括弧和下划线";
        }
        //验证发票名称
        if (!preg_match('/^((\d{6}[0-9A-Z]{9})|([0-9A-Za-z]{2}\d{6}[0-9A-Za-z]{10})|([0-9A-Za-z]{20}))$/', $dataArray['identification'])) {
            return "纳税人识别号不正确";
        }
        $dataDraw['invoice_name'] = $dataArray['invoice_name'];
        $dataDraw['identification'] = $dataArray['identification'];
        $dataDraw['phone'] = $dataArray['phone'];
        $dataDraw['address'] = $dataArray['address'];
        $dataDraw['bank'] = $dataArray['bank'];
        $dataDraw['number'] = $dataArray['number'];
//dd($dataArray);
        $DrawCommonmodel = new DrawCommon();
        $result = $DrawCommonmodel->BaseUpdate($dataDraw,['uid'=>$adminId,'type'=>2]);

        if ($result) {
            return '保存成功';
        } else {
            return '尚未做修改操作';
        }

    }

    //院系
    public function subjectsadd(Request $request)
    {
        $adminId = session('rootId');
        $dataArray = $request->param();
        if (empty($dataArray['pid'])) {
            return -1;
        }
        $dataAll = [
            'academy_name' => $dataArray['academy_name'],
            'pid' => $dataArray['pid'],
            'create_time' => time(),
            'admin_id' => $adminId
        ];
        $result = $this->Collegerecord->dataAdd($dataAll);
        return $result ? $result : false;
    }

    //院系学员
    public function userShow($id)
    {
        return $this->User->subjectsShow($id);
    }

    //删除
    public function subjectsDelete(Request $request)
    {
        $id = $request->param('id');
        $result = $this->Collegerecord->dataDelete($id);
        return $result ? true : false;
    }

    //保存
    public function saveName(Request $request)
    {
        $adminId = session('rootId');
        $dataArray = $request->param();
        $dataAll = [
            'academy_name'=> $dataArray['academy_name'],
            'create_time' => time(),
            'id' => $dataArray['id'],
            'admin_id' => $adminId
        ];
        $result = $this->Collegerecord->dataSave($dataAll);
        return $result ? true : false;
    }
}