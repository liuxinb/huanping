<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/6/14
 * Time: 下午10:08
 */

namespace app\common\model;

class TeaTeacher extends Base
{
    /***
     * 获取teaterther数据
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    function showTeaCherData()
    {
        //获取教师数据表的id，name值
        $teacherData = $this->field("id,name")->select();
        return $teacherData;
    }

    /***
     * 获取teaterther数据
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    function selectTeaCherData($map)
    {
        //获取教师数据表的id，name值
        $teacherData = $this->where($map)->paginate();
        return $teacherData;
    }

    /***
     * 添加teaterther数据
     * @param $data 添加的数据
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    function insertTeaCherData($data)
    {
        //获取教师数据表的id，name值
        $teacherData = $this->insert($data);
        return $teacherData;
    }

    /***
     * 添加教师和修改教师公用方法
     * @param $data 添加的数据
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    public function teaPub() {
        $data = input('post.');
        if (($data['sex'][0]) === '男') {
            $data['sex'] = 1;
        } elseif (($data['sex'][0]) === '女') {
            $data['sex'] = 0;
        }
        $data['create_time'] = time();
        return $data;
    }

    /***
     * 添加教师和修改教师公用方法
     * @param $data 添加的数据
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    public function updateTeaCherData($data) {
        $data = $this->update($data);
        return $data;
    }

    /***
     * 根据条件删除教师数据
     * @param $map 条件 id
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    public function deleteTeaCherData($map) {
        $data = $this->where($map)->delete();
        return $data;
    }








}