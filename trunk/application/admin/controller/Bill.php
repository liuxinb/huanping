<?php
namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\Invoice;
use think\Request;

/**
* 发票管理
*/
class Bill extends Adminbase
{
    //model
    private $Invoice;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Invoice =new Invoice();
    }

	//发票信息列表
	public function index(){
        $data=$this->Invoice->showBillData();
        return view('',['all'=>$data]);
    }

    //发票详情
    public function detail_invoice($id){
        $map=['id'=>$id];
        $data= $this->Invoice->showBillDetailData($map);
        return view('',['all'=>$data]);
    }


}