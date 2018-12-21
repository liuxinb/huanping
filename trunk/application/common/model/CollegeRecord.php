<?php
/**
 * Created by PhpStorm.
 * User: liuxin
 * date: 2018/8/30
 * Time: 下午15:17
 */

namespace app\common\model;

use app\common\model\User;
use think\Db;

class CollegeRecord extends Base
{
    /***
     * 查询当前院校，和当前院校下的所有院系
     * @param $id
     * @param string $field
     * @return array
     */
    public function dataSelect($id, $field = '*')
    {
        $recruitmentData = $this
            ->field($field)
            ->where('admin_id', $id)
            ->where('pid', 0)
            ->find();
        $recruitmentSubjects = $this
            ->field('*')
            ->where('pid', $recruitmentData->id)
            ->select();
        if ($recruitmentData) {
            return array(
                [
                    'academy_name' => $recruitmentData->academy_name,
                    'pid' => $recruitmentData->id,
                    'subjectsObject' => $recruitmentSubjects
                ]);
        } else {
            return array([
                'academy_name' => '',
                'pid' => '',
                'subjectsObject' => ''
            ]);
        }
    }

    public function dataSaveAll($data)
    {

        return $this
            ->saveAll($data);
    }
    
    public function dataSave($data)
    {
        if ($data['id']) {
            return $this
                ->isUpdate(true)
                ->data($data, true)
                ->save();
        } else {
            return $this
                ->isUpdate(false)
                ->data($data, true)
                ->save();
        }
    }
    

    public function dataAdd($data)
    {
//        Db::startTrans(); //开启事务
//        try {
            $result = $this->create($data);
//            $this->where('id',$data['pid'])->update(['state'=> '0']);
//            Db::commit(); //提交
        if ($result) {
            return $result;
        }
//        } catch (\Exception $e) {
//            Db::rollback(); //回滚
//        }

    }

    public function dataDelete($id)
    {
        $user = new User();
        Db::startTrans(); //开启事务
        try {
            $this
                ->where('id', $id)
                ->delete();
            $user
                ->where('subjects_id', $id)
                ->update(['subjects_id' => '0']);
            Db::commit(); //提交
            return true;
        } catch (\Exception $e) {
            Db::rollback(); //回滚
        }
        return false;
    }

    /***
     * 统计报名人数 学完人数
     * @param $id
     * @param string $field
     * @return array
     */
    public function statistical($id, $field = '*')
    {
        $userModel = new User();
        $recruitmentData = $this
            ->field($field)
            ->where('admin_id', $id)
            ->where('pid', 'neq', 0)
            ->select();
        if ($recruitmentData) {
            $dataAll = [];
            $array = [];
            foreach ($recruitmentData as $k => $v) {
                $array =
                    [
                        'dataName' => $recruitmentData[$k]->academy_name,
                        'dataAll' => $userModel->subjectsShow($recruitmentData[$k]->id),
                        'payAll' => $userModel->subjectsShow($recruitmentData[$k]->id, true)
                    ];
                $dataAll[] = $array;
            }
            return $dataAll;
        }
    }

    /***
     * 获取CollegeRecord树型数据
     * @param $map   院校条件
     * @return $data
     * @data 2018/9/4 xuweiqi
     */
    function CollegeRecordGetTreeData($map)
    {
        $data = $this->where($map)
            ->getTreeData('tree', '', 'academy_name');
        return $data;
    }

    public function firmsignGetData(){
        $data=$this->alias('c')
            ->join('__FIRMSIGN__ f','c.admin_id=f.id')
//            ->where('c.pid=0')
            ->field('f.email')
            ->select();
        return $data;
    }


    /***
     * 查找CollegeRecord数据
     * @param $id   传入的id
     * @data 2018/7/4 xuweiqi
     */
    function findCollegeRecordData($id)
    {
        $data = $this->where(['pid' => $id])->find();
        return $data;
    }

    /***
     * 删除CollegeRecord数据
     * @param $id   传入的id
     * @data 2018/7/4 xuweiqi
     */
    function delCollegeRecordData($id)
    {
        $result = $this->where(['id' => $id])->delete();
        return $result;
    }

    /***
     * 修改collegerecord数据
     * @param $where  条件
     * @param $save   修改数据
     */
    public function saveCollegeListByFirmNum($where, $save)
    {
        return $this
            ->where($where)
            ->update($save);
    }


    /***
     * 查询院校数据
     * @param $id   传入的id
     * @data 2018/7/4 xuweiqi
     */
    public function selectAcademyData($mapdata){
        $field='f.id,f.email,f.type,c.id as cid,c.state,c.academy_name,c.create_time,c.pid,c.organization_code';
        $map='f.type=2';
        $mapPid="c.pid=0";
        $mapData=$mapdata;
        $order='c.state asc';
        $data=$this::alias('c')
            ->join("__FIRMSIGN__ f",'f.id=c.admin_id','left')
            ->field($field)
            ->where($map)
            ->where($mapData)
            ->where($mapPid)
            ->order($order)
//            ->getTreeData('tree', '', 'academy_name');
            ->paginate();
        return $data;
    }

    /***
     * 获取院校的系的id
     * @param $id
     * @return string
     * @user xuweiqi
     */
    public function getCollegeId($id)
    {
        $array = $this
            ->where('pid', $id)
            ->select();
        $datas=[];
        foreach ($array as $key => $item) {
               $datas[$key]['academy']= $item->academy_name;
        }
        return $datas;
    }

    public  function reduceArray($array) {
        $return = [];
        array_walk_recursive($array, function ($x) use (&$return) {
            $return[] = $x;
        });
        return $return;
    }


    public function dataCreate($data)
    {
        return $this->create($data);
    }

    public function subjectsid($name,$id)
    {
        //field id where academy_name=院系名 and pid !=0 admin_id = 院校账号id
        $name = $this
            ->field('id')
            ->where('academy_name',$name)
            ->where('pid','neq','0')
            ->where('admin_id',$id)
            ->find();
        if ($name) {
            return $name->id;
        }
        return false;
    }

    public function selectfind($id,$field='*')
    {
        return $this
            ->field($field)
            ->where('pid',0)
            ->where('admin_id',$id)
            ->find();
    }
}