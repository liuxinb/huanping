<?php
namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\Record;//命名空间
use app\common\model\UserDetail;
use app\common\model\Recruitment;  //招聘信息表
use think\Request;
use think\validate;

/**
* 企业信息控制器
*/
class Firm extends Adminbase
{
    //model
    private $Record;
    private $Recruitment;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Record =new Record();
        $this->Recruitment =new Recruitment();
    }
  
     //企业信息列表
    public function index(){
        $data=  Request::instance()->param();
        //获取查询条件
        $map = array();
        if(isset($data['keyword'])){
            $map['firmname']  = array('like', '%'.input('keyword').'%');
        }
        //档案表 员工表 一对一关系
        $dangan = $this->Record->selectRecordData($map);
        //根据条件查询结果为空时
        if(empty($dangan)){
            return view('',['all'=>$data,'render'=>$dangan]);
        }
        $data=$this->Record->index_pub($dangan);
        return view('',['all'=>$data,'render'=>$dangan]);
    }


    //企业信息查看详情
    public function detail_firm($id){
        $map=array('id'=>$id);
        //根据条件查询数据
        $data=$this->Record->selectRecordData($map);
        return view('',['all'=>$data]);
    }

    //企业信息修改
    public function edit_firm($id){
        $map=array('id'=>$id);
        //根据条件查询数据
        $data=$this->Record->selectRecordData($map);
        return view('',['all'=>$data]);
    }

    
    protected $msg = [
        'firmname.require' => '企业名称不能为空',
        'registersite.require' => '注册地址不能为空',
        'invoicename.require' => '发票名称不能为空',
        'identifynumber.require' => '纳税人识别号不能为空',
        'identifynumber.max' => '纳税人识别号最长不能超出20位',
        'identifynumber' => '请输入正确的纳税人识别号',
        'addressphone.require' => '地址电话不能为空',
        'addressphone' => '电话格式错误',
        'name.require' => '姓名不能为空',
        'phone.require' => '手机不能为空',
        'invoiceaddress.require' => '发票地址不能为空',
        'openingbank.require' => '开户行不能为空',
        'accountnumber.require' => '账号不能为空',
        'accountnumber' => '账号格式不正确',
        'phone' => '手机号格式不正确',
        'email.require' => '邮箱不能为空',
        'email' => '邮箱格式不正确',
    ];
    
    // 企业信息修改执行
    public function edit_firm_run($id){
    	if(Request::instance()->isPost()){
              $data=input("post.");
              $validate = new Validate([
                  'firmname' => 'require',
                  'registersite' => 'require',
                  'invoicename' => 'require',
                  'invoiceaddress' => 'require',
                  'openingbank' => 'require',
                  'accountnumber' => ['/^([1-9]{1})(\d{14}|\d{18})$/', 'require'],
                  'identifynumber' => 'require|max:20',
                  'identifynumber' => ['/^((\d{6}[0-9A-Z]{9})|([0-9A-Za-z]{2}\d{6}[0-9A-Za-z]{10})|([0-9A-Za-z]{20}))$/'],
                  'addressphone' => ['/^((0\d{2,3}-\d{7,8})|(1[35678]\d{9}))$/','require'],
                  'name' => 'require',
                  'phone' => ['/^1[3456789]\d{9}$/', 'require'],
                  'email' => ['^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$', 'require'],
              ], $this->msg);
            if (!$validate->check($data)) {
                return $this->error($validate->getError(), 'index');
            }
            $map=array('id'=>$id);
            $request=$this->Record->updateRecordData($map,$data);
              if($request){
              	$this->success("修改成功","index");
              }else{
              	$this->error("您没有做任何修改","index");
              }
    	}else{
    		$this->error("未知请求或没有权限",'home/index/index');
    	}
    }


    //查看招聘信息列表
    public function indexRecruit($id){
//        $data=  Request::instance()->param();
//        //获取查询条件
//        $map = array();
//        if(isset($data['work'])){
//            $map['work']  = array('like', '%'.input('work').'%');
//        }
        $data=$this->Recruitment->dataAll($id);
        return view('',['dataAll'=>$data]);
    }

    //查看企业招聘信息 详情
    public function detailRecruit(Request $request){
        $id = $request->param('id');
        $dataAll = $this->Recruitment->find($id);
        $dataAll->work_describe = str_replace("&amp","\r\n",$dataAll->work_describe);
        $dataAll->email_size = str_replace("||","",$dataAll->email_size);
        return view('',['One'=>$dataAll]);
    }

    //删除企业招聘信息
    public function deleteRecruit(Request $request){
        $id = $request->param('id');
        $result = $this->Recruitment->dataDelete($id);
        return $result ? true : false;
    }

    //修改状态 撤销和发布
    public function stateRecruit(Request $request)
    {
        $data = $request->param();
        $dataArray = [
            'id' => $data['id'],
            'state' => $data['state'],
        ];
        $result = $this->Recruitment->dataUpdate($dataArray);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}

