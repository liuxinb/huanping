<?php
namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\CollegeRecord;
use app\common\model\Firmsign;
use app\common\model\User;
use MongoDB\Driver\ReadConcern;
use think\Request;

/**
 * 院校管理
 */
class Academy extends Adminbase
{
    //model
    private $CollegeRecord;
    private $Firmsign;
    private $User;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->CollegeRecord = new CollegeRecord();
        $this->Firmsign = new Firmsign();
        $this->User = new User();
    }

    public $States = array( 1 => '已审核', 0 => '未审核');

     //获取院校CollegeRecord树型数据
    public function index(){
//        $data=  Request::instance()->param();
//        //获取查询条件
//        $map = array();
//        if(isset($data['name'])){
//            $map['academy_name']  = array('like', '%'.input('name').'%');
//        }
        //获取CollegeRecord树型数据
        $data = $this->CollegeRecord->CollegeRecordGetTreeData();
        return view('',[ 'data' => $data]);
    }

    /**
     * 删除子类
     */
    public function delete($id) {
        $data = $this->CollegeRecord->findCollegeRecordData($id);
        if ($data) {
            $this->error('请先删除系', 'index');
        }
        //判断user表是否有联系院系
        $findUserData=$this->User->findUserListByFirmNum(['subjects_id'=>$id]);
        if(!empty($findUserData)){
            //如果有院系 就更新为空
            $findUserSub=$this->User->saveUserListByFirmNum(["subjects_id"=>$id],array("subjects_id"=>0));
            if($findUserSub){
                $result =$this-> CollegeRecord->delCollegeRecordData($id);
                if ($result) {
                    $this->success('删除成功', 'index');
                } else {
                    $this->error('请先删除系','index');
                }
            }
        }else{
            $result =$this-> CollegeRecord->delCollegeRecordData($id);
            if ($result) {
                $this->success('删除成功', 'index');
            } else {
                $this->error('请先删除系','index');
            }
        }

    }



    //院校审核页面
    public function indexAudit(){
        $data=  Request::instance()->param();
        //获取查询条件
        $map = array();
        if(isset($data['states'])|| isset($data['email'])|| isset($data['name'])){
            $map['c.state']  = array('like', '%'.input('states').'%');
            $map['f.email']  = array('like', '%'.input('email').'%');
            $map['c.academy_name']  = array('like', '%'.input('name').'%');
        }
        //查询院校数据
        $data = $this->CollegeRecord ->selectAcademyData($map);
        return view('',['all'=>$data,'State'=>$this->States]);
    }

    const freeze = 0;
    const unfreeze = 1;

    //院校审核
    public function unfreezeState($id, $state)
    {
        $staffmessage = $this->CollegeRecord->find($id);
        $state == self::unfreeze ? $staffmessage->state = self::unfreeze : $staffmessage->state = self::freeze;
        $result = $staffmessage->save();
        if ($result) {
            return ['status'=>1,'msg'=>'操作成功'];
        } else {
            return ['status'=>0,'msg'=>'操作频繁，稍后再试'];
        }
    }

    //查看院校信息
    public function getSchoolInfo(){
        if(Request::instance()->isPost()){
            $data=input('post.');
            $cId = $this->CollegeRecord->find($data['id']);
            $arryx=[];
            $arryx['id']=$cId['id'];
            $arryx['yx']=$cId['academy_name'];
            $arryx['dm']=$cId['organization_code'];
            $arryx['state']=$cId['state'];
            $name = $this->CollegeRecord->getCollegeId($data['id']);
//            dump($name);die;
//            $names=array_merge($arryx,$name);
//            $names=$this->CollegeRecord->reduceArray($name);
//            $names=array_merge($arryx,$names);
//            dump($name);die;
            return ['school'=>$arryx,'academy'=>$name];
        }
    }




}