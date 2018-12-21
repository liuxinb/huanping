<?php
namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\Certificate;
use think\Request;

/**
 * 发票管理
 */
class Ccie extends Adminbase
{
    //model
    private $Certificate;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Certificate =new Certificate();
    }

    //发票信息列表
    public function index(){
        $data=$this->Certificate->showCertificateData();
        return view('',['all'=>$data]);
    }
}