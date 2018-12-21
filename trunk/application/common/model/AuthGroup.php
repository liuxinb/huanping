<?php
namespace app\common\model;

/**
* auth_group 表模型
*/
class AuthGroup extends Base
{
    /***
     * 查询Authgroup全部数据
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function selectAuthGroupData()
    {
        $result=$this->select();
        return $result;
    }

    /***
 * 插入角色Authgroup全部数据
     * @data 数据集
 * @return data
 * @data 2018/7/5 xuweiqi
 */
    function insertAuthGroupData($data)
    {
        $result=$this->insert($data);
        return $result;
    }

    /***
     * 更新角色Authgroup title数据
     * @param $data 数据集 id
     * @param $datas 数据集
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function updataAuthGroupData($map,$datas)
    {
        $result=$this->where($map)->update($datas);
        return $result;
    }

    /***
     * 删除角色Authgroup数据
     * @param $map 条件 id
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function deleteAuthGroupData($map)
    {
        $result=$this->where($map)->delete();
        return $result;
    }

    /***
     * 查找角色Authgroup数据
     * @param $map 条件 id
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function findAuthGroupData($map)
    {
        $result=$this->where($map)->find();
        return $result;
    }


}