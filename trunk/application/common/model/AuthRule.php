<?php
namespace app\common\model;

/**
* think_auth_rule 表模型
*/
class AuthRule extends Base
{
    /***
     * 获取AuthRole 树型数据
     * @return $data
     * @data 2018/7/5 xuweiqi
     */
    function AuthRoleGetTreeData()
    {
        //权限菜单管理sort排序
        $data = $this->getTreeData('tree', 'sort', 'title');
        return $data;
    }

    /***
     * 添加权限 插入AuthRole 数据
     * @param $data   插入数据
     * @return bool
     * @data 2018/7/5 xuweiqi
     */
    function insertAuthRoleData($data)
    {
        $data =$this->insert($data);
        return $data;
    }
    /***
     * 修改权限 修改AuthRole 数据
     * @param $data   修改数据
     * @return bool
     * @data 2018/7/5 xuweiqi
     */
    function updateAuthRoleData($data)
    {
        $map=["id"=>$data['id']];
//        $info=['title'=>$data['title'],'name'=>$data['name']];
        unset($data['id']);
        $result=$this->where($map)->update($data);
        return $result;
    }

    function updateSort($data){
        $info=['sort'=>$data['sort']];
        // $result=new auth_rule;
        $result=$this->where(["id"=>$data['id']])->update($info);
        return $result;
    }

    /***
     * 根据条件查找AuthRole 数据
     * @param $map   条件 Pid
     * @return bool
     * @data 2018/7/5 xuweiqi
     */
    function findAuthRoleData($id)
    {
        $data=$this->where(['pid'=>$id])->find();
        return $data;
    }

    /***
     * 根据条件删除AuthRole 数据
     * @param $map   条件 id
     * @return bool
     * @data 2018/7/5 xuweiqi
     */
    function deleteAuthRoleData($map)
    {
        $result=$this->where($map)->delete();
        return $result;
    }

    /***
     * 获得AuthRole树 数据
     * @return $data
     * @data 2018/7/5 xuweiqi
     */
    function levelAuthRoleGetTreeData()
    {
        $result=$this->getTreeData('level','id','title');
        return $result;
    }

}