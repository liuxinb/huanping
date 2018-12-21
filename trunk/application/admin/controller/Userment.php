<?php
namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\UserDetail;
use think\Request;
use app\common\model\User;
use think\Validate;

/**
* 用户管理控制器
*/
class Userment extends Adminbase
{
    //model
    private $UserDetail;
    private $User;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->UserDetail =new UserDetail();
        $this->User = new User();
    }

    const freeze = -1;
    const unfreeze = 1;

    //用户管理模板
    public function index(){
        //  接收表单值
        $data=  Request::instance()->param();
        $map = array();
        //获取查询条件
        if(isset($data['phone']) || isset($data['idnumber']) || isset($data['name'])){
            $map['ud.name']  = array('like', '%'.input('name').'%');
            $map['ud.idnumber']  = array('like', '%'.input('idnumber').'%');
            $map['u.phone']  = array('like', '%'.input('phone').'%');
        }
        //获取用户管理数据
        $data=$this->UserDetail->ShowUserDetailDate($map);
        $res=$this->UserDetail-> hiddenPhone($data);
//        dump($res);die;
        return view('',['all' => $res,'render'=>$data]);
    }

    protected $msg = [
        'name.require' => '姓名不能为空',
        'password.require' => '密码不能为空',
        'idnumber.require' => '身份证号不能为空',
        'idnumber' => '身份证格式错误',
        'phone.require' => '手机号不能为空',
        'phone' => '手机号格式不正确',
        'create_time.require' => '入职时期不能为空',
        'create_time' => '入职时期格式错误',
    ];

    protected $validate = [
        'name' => 'require',
        'phone' => ['/^1[34578]\d{9}$/', 'require'],
//        'password' => 'require',
        'idnumber' => ['/(^\d(15)$)|((^\d{18}$))|(^\d{17}(\d|X|x)$)/', 'require'],
        'create_time' => ['/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', 'require']
    ];

	//删除用户
	public function del_userment($id){
		if(empty($id)){
			$this->error('请选择要操作的数据!');
		}
		//删除user_detail数据
		$res=$this->UserDetail->DelUsermentData($id);
        if($res){
            //删除user数据
            $req=$this->UserDetail->DelUserData($id);
            if($req){
                  $this->success("删除用户成功",'index');
            }else{
                  $this->error("删除用户失败",'index');
            }
        }else{
                  $this->error("删除用户失败",'index');
        }
	}

    //批量删除
	public function batchDelete($ids){
//	   $data= $this->UserDetail->where(['uid'=> $ids])->field()->find();
//        $result = $this->UserDetail->destroy($ids);
        $result = $this->UserDetail->destroy($ids);
        if($result){
//            $result = model('User')->destroy($ids);
            return json(["success"=>"删除成功","ids"=>$result]);
        }else{
            return json(["error"=>"删除失败","ids"=>$result]);
        }

    }

//
//    //修改学员
//    public function editUserment()
//    {
//        $data=input('post.');
//        dump($data);die;
//        $data = $this->User->find($id);
//        $data->name = $data->adminUserDetail->name;
//        $data->create_time = date('Y-m-d', strtotime($data->adminUserDetail->create_time));
//        $data->idnumber = $data->adminUserDetail->idnumber;
////        $data->page_number = $page_number;
//        return json($data);
//    }

     //修改保存员工数据
    public function editUserment(Request $request)
    {
        if($request->isPost()){
            //获取当前企业ID
//        $enterpriseId = session('rootId');
            //请求
            $data = $request->param();  //uid
            //验证数据合法性
            $validate = new Validate($this->validate, $this->msg);
            if (!$validate->check($data)) {
                $this->error($validate->getError(),'index');
            }
            //验证手机号唯一性
//            $userid = $request->only('uid')['uid'];
            $onlyphone = $this->User->onlyUser($data['phone'], 'phone', $data['uid']);
            if (count($onlyphone)>=2) {
                $this->error("手机号已存在",'index');
            }
            //验证身份证真实性
            if (!validation_filter_id_card($data['idnumber'])) {
                $this->error("身份证不合法",'index');
            }
            //验证身份证唯一性
            $onlyphone = $this->UserDetail->onlyUserdetail($data['idnumber'], 'idnumber', $data['uid']);
            if (count($onlyphone)>=2) {
                $this->error("身份证号已存在",'index');
            }
            //处理数据user数据
            $users=[
                'id'=>$data['uid'],
                'phone'=>$data['phone'],
            ];
            if (!empty($data['password'])) {
                $users['password'] = md5($data['password']);
            }else{
                unset($data['password']);
            }
            //处理userDetails 数据
            $userDetails = [
                'name' =>$data['name'],
                'idnumber' => $data['idnumber'],
                'create_time' => $data['create_time'],
            ];
            $uid = $data['uid'];
            //数据保存操作 ?page=8
            $userResult = $this->User->userUpdate($users, $userDetails, $uid);
            //响应
            return $this->success($userResult,'index');
        }
    }


    //员工状态
    public function unfreezeStatus($id, $status)
    {
        $staffmessage = $this->User->find($id);
        $status == self::unfreeze ? $staffmessage->status = self::unfreeze : $staffmessage->status = self::freeze;
        $result = $staffmessage->save();
        if ($result) {
            return '操作成功';
        } else {
            return '操作频繁，稍后再试';
        }
    }
	
	
}