<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/5/29
 * Time: 下午12:04
 */

namespace app\common\model;

use think\Db;

class PlugList extends Base
{
    function showAdvert()
    {
        return $this->field('plug_pic')
            ->where('plug_status', 'eq', '1')
            ->where('plug_typeid', 'eq', '2')
            ->order('plug_order desc')
            ->select();

    }

    /**
     * 查询广告数据
     * @param $table 数据表名
     * @param $data  需要插入多条数据的数据
     * @return       返回成功插入数据条数
     */
    public function paginatePlugListData()
    {
        $data = $this->paginate();
        return $data;
    }

    /**
     * 添加广告数据
     * @param $table 数据表名
     * @param $data  需要插入多条数据的数据
     * @return       返回成功插入数据条数
     */
    public function insertPlugListData($data)
    {
        $data = $this->insert($data);
        return $data;
    }

    /**
     * 查找数据
     * @param $map 条件
     * @return  $data
     */
    public function findPlugListData($map)
    {
        $data = $this->find($map);
        return $data;
    }

    /**
     * 更新广告数据
     * @param $map 条件
     * @param $data 更新的数据
     * @return  $data
     */
    public function updatePluglistData($map, $data)
    {
        $data = $this->where($map)->update($data);
        return $data;
    }

    /**
     * 删除广告数据
     * @param $map 条件
     * @return  bool
     */
    public function deletePluglistData($map)
    {
        $data = $this->where($map)->delete();
        return $data;
    }

    /***
     * 获取广告列表
     * user: liuxin
     * date: 2018/7/9
     * time: 下午16:34
     */
    public function getList($where = '', $field = "*", $find = 'select')
    {
//        $this->table = empty($table)?$this->table:$table;
        return $this
            ->where($where)
            ->field($field)
            ->$find();
    }

    /***
     * 更新数据加1
     * user: liuxin
     * date: 2018/7/9
     * time: 下午16:34
     */
    public function saveOne($id, $table, $field)
    {
        $this->table = empty($table) ? $this->table : $table;
        return Db::table($this->table)
            ->where($id)
            ->setInc($field, 1);
    }

    /***
     * 添加数据
     * user: liuxin
     * date: 2018/7/9
     * time: 下午16:34
     */
    public function addOne($data, $table)
    {
        $this->table = empty($table) ? $this->table : $table;
        return Db::table($this->table)
            ->insert($data);
    }

    /***
     * 两表联查
     * user: liuxin
     * date: 2018/7/9
     * time: 下午16:34
     */
    public function getListJoin($where, $table = '', $field = "*", $jointable)
    {
        $this->table = empty($table) ? $this->table : $table;
        return Db::table($this->table)
            ->alias("c")
            ->where($where)
            ->join($jointable . " t", "c.teacher_id=t.id", "left")
            ->field("c.id,c.name,c.title,c.description,c.bag_img,c.bag_price,c.hours,c.pid,t.name as teacher_name,t.qg,t.referral,t.sex,t.phone")
            ->select();
    }

    /***
     * 多对一，老师多个课程
     * user: liuxin
     * date: 2018/7/9
     * time: 下午16:34
     */
    public function getListForeach($arrCategoryList)
    {
        $parentArray = [];
        foreach ($arrCategoryList as $item) {
            if ($item['pid'] == 0) {
                array_push($parentArray, $item);
            }
        }
        foreach ($parentArray as $key => $parent) {
            $chiled = [];
            foreach ($arrCategoryList as $item) {
                if ($item['pid'] == $parent["id"]) {
                    $chiled[] = $item;
                }
            }

            if ($chiled == []) {

                unset($parentArray[$key]);
            } else {
                $parentArray[$key]["childs"] = $chiled;

            }
        }
        return $parentArray;
    }


}