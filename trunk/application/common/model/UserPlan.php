<?php

namespace app\common\model;

use app\common\model\EnterpriseUser;
use app\common\model\EnterpriseUserdetail;
use think\Db;

class UserPlan extends Base
{

    /**
     * 获取我的课程的百分比
     * @param $cid
     * @param $count
     * @param $uid
     * @return bool|string
     * @user 李海江 2018/7/19~下午8:00
     */
    function getBaifenBi($cid, $count, $uid)
    {
        if ($count == 0) return false;
        $data = $this
            ->field('mid')
            ->where('pid', $cid)
            ->where('uid', $uid)
            ->where('complete', '1')
            ->select();
        $one_count = count(array_unique($data));
        $baifenbi = floor($one_count / $count * 100) . '%';
        return $baifenbi;
    }

    /***
     * 获取受培训的员工id
     * @param $id
     * @return string
     * @user liuxin
     */
    public function getUserId($id)
    {
        $array = $this
            ->field('uid')
            ->where('train_id', $id)
            ->select();
        $data = '';
        foreach ($array as $k => $v) {
            $data .= $v['uid'] . ',';
        }
        return $data;
    }

    /***
     *  admin获取所有员工uid
     * @param $id
     * user xuweiqi
     */
    public function adminGetUserId()
    {
        $array = $this
            ->field('uid')
            ->select();
        $data = '';
        foreach ($array as $k => $v) {
            $data .= $v['uid'] . ',';
        }
        return $data;
    }

    /***
     * 检查用户是否存在,存在更新数据，不存在返回用户id
     * @param $uidArray
     * @param $trainId
     * @return array|bool
     * @throws \Exception
     */
    public function queryUserId($uidArray, $trainId, $cid)
    {
        $data = [];
        $updateData = [];
        foreach ($uidArray as $k => $v) {
            $result = $this
                ->field('id')
                ->where('type', 'neq', '1')
                ->where('pid', 0)
                ->where('cid', $cid)
                ->where('uid', $v)
                ->find();
            if ($result) {
                $updateData[] = [
                    'id' => $result->id,
                    'type' => '1',
                    'train_id' => $trainId,
                ];
            } else {
                $data[] = $v;
            }

        }

        if ($updateData) {
            $this->saveAll($updateData);
        }
        return $data ? $data : false;
    }

    /***
     * 添加培训计划
     * @param $data
     * @return array|false
     * @throws \Exception
     * user liuxin
     */
    public function userplanCreateData($data)
    {
        $trainModel = new Train();
        $uidArray = empty($data['uid']) ? false : explode(",", $data['uid']);
        $trainArray = [
            'cid' => $data['cid'],
            'name' => $data['name'],
            'start_time' => $data['create_time'],
            'create_time' => date("Y-m-d H:i:s"),
            'end_time' => $data['update_time'],
            'firm_id' => session('rootId'),
            'count' => empty($uidArray) ? 0 : count($uidArray),
        ];
        Db::startTrans(); //开启事务
        try {
            //集中培训
            $trainId = $trainModel->createTrainData($trainArray);

            //用户存在更新数据，不存在返回用户id || 用户为空不执行
            if ($uidArray && $trainId) {
                $uidArray = $this->queryUserId($uidArray, $trainId, $data['cid']);
                if (!$uidArray) {
                    Db::commit(); //提交
                    return true;
                }
                $userplanData = array_map(function ($uid) use ($data, $trainId) {
                    return [
                        'uid' => $uid,
                        'create_time' => $data['create_time'],
                        'update_time' => $data['update_time'],
                        'cid' => $data['cid'],
                        'type' => '1',
                        'train_id' => $trainId,
                    ];
                }, $uidArray);
                $this->saveAll($userplanData, false);
            }

           Db::commit(); //提交
           return true;

        } catch (\Exception $e) {
            Db::rollback(); //回滚
            throw $e;
        }
        return false;
    }

    /***
     * 返回集中培训列表
     * @return mixed
     * user liuxin
     */
    public function getUsersplanData()
    {
        $data = $this
            ->join('hp_flv_category flc', 'flc.id = hp_user_plan.cid')
            ->field('hp_user_plan.cid,hp_user_plan.create_time,hp_user_plan.train_name,flc.bag_img')
            ->select();
        return $data;
    }

    /***
     * 获取集中培训学员进度
     * @user liuxin
     */
    public function getTrainPlan($uid)
    {
        $data = $this
            ->field('id')
            ->where('type', '1')
            ->where('uid', $uid)
            ->count();
        return $data <= 0 ? false : true;
    }
}