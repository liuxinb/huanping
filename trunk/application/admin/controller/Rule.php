<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use app\common\model\AuthGroup;
use app\common\model\AuthRule;
use think\Db;
use think\Request;
/**
 * 
 * 后台权限管理
 */
class Rule extends AdminBase{

    //model
    private $AuthRule;
    private $AuthGroup;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->AuthRule =new AuthRule();
        $this->AuthGroup =new AuthGroup();

    }
//******************权限***********************

    /*权限菜单列表*/
    public function rule_list(){
        //权限菜单列表 title树形结构 sort排序
        $data=$this->AuthRule->AuthRoleGetTreeData();
        return view('',['data'=>$data]);
    }

    /**
     * 添加权限
     */
    public function add(){
        $data=input('post.');
        unset($data['id']);
        $result=$this->AuthRule->insertAuthRoleData($data);
        if ($result) {
            return $this->success('添加成功','Admin/Rule/rule_list');
        }else{
            return $this->error('添加失败','rule_list');
        }
    }

    /**
     * 修改权限
     */
    public function edit(){
        $data=input('post.');
        //修改title name数据
        $result=$this->AuthRule->updateAuthRoleData($data);
        if ($result) {
            $this->success('修改成功','Admin/Rule/rule_list');
        }else{
            $this->error('您没有做任何修改','rule_list');
        }
    }


    /**
     * 删除权限
     */
    public function delete($id){
        $map=array('id'=>$id);
        $data=$this->AuthRule->findAuthRoleData($id);
        if ($data) {
            $this->error('请先删除子权限', 'rule_list');
        } 
        $result=$this->AuthRule->deleteAuthRoleData($map);
        if($result){
            $this->success('删除成功','Admin/Rule/rule_list');
        }else{
            $this->error('请先删除子权限','Admin/Rule/rule_list');
        }

    }

    /**
     * 角色列表
     */
    public function rule_group(){
        //查询AuthGroup全部数据
        $data=$this->AuthGroup->selectAuthGroupData();
        $assign=array('data'=>$data);
        return view('',$assign);
    }


     /**
     * 添加角色
     */
    public function add_group(){
        $data=input('post.');
        unset($data['id']);
        //插入角色数据
        $result=$this->AuthGroup->insertAuthGroupData($data);
        if ($result) {
            $this->success('添加成功','Admin/Rule/rule_group');
        }else{
            $this->error('添加失败','rule_group');
        }
    }

    /**
     * 修改角色
     */
    public function edit_group(){
        $data=input('post.');
        //参数
        $map=["id"=>$data['id']];
        $datas=['title'=>$data['title']];
        //根据id更新title数据
        $result=$this->AuthGroup->updataAuthGroupData($map,$datas);
        if ($result) {
            $this->success('修改成功','Admin/Rule/rule_group');
        }else{
            $this->error('您没有做任何修改','rule_group');
        }
    }

    /**
     * 删除角色
     */
    public function delete_group($id){
        if ($id==1) {
            $this->error('该分组不能被删除');
        }
        $map=array('id'=>$id);
        $result=$this->AuthGroup->deleteAuthGroupData($map);
        if ($result) {
            $this->success('删除成功','Admin/Rule/rule_group');
        }else{
            $this->error('删除失败','rule_group');
        }
    }


    /**
     * 分配权限
     */
    public function rule_distribution($id){
        if(Request::instance()->post()){
            $data=input('post.');
            $map=  ['id'=>$data['id']];
            //获取权限id用逗号分隔
            $datas['rules']=implode(',', $data['rule_ids']);
            //根据id更新字段rules数据
            $result=$this->AuthGroup->updataAuthGroupData($map,$datas);
            //判断结果
            if ($result) {
                $this->success('操作成功','Admin/Rule/rule_group');
            }else{
                $this->error('您没有修改数据','rule_group');
            }
        }else{
            $map=array('id'=>$id);
            //根据条件查找AuthGroup所有数据
            $group_data=$this->AuthGroup->findAuthGroupData($map);
            //获取规则数据,以逗号分隔为数组
            $group_data['rules']=explode(',', $group_data['rules']);
            // 获取树结构
            $rule_data=$this->AuthRule->levelAuthRoleGetTreeData();
            //渲染数据
            return view('',['group_data'=>$group_data,'rule_data'=>$rule_data]);
        }
    }

}
