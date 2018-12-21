<?php

namespace app\common\model;

class Exam extends Base
{
    protected $autoWriteTimestamp = true;

    /**
     * 查询exam数据
     * @param $map 搜索条件
     * @return $data
     */
    function selectExamData($map)
    {
        $field = "e.id,e.results,e.isqualified,e.create_time,e.update_time,c.title,u.phone";
        $data = $this->alias("e")
            ->join("__USER__ u", "e.uid=u.id", "left")
            ->join("__FLV_CATEGORY__ c", "e.cid=c.id", "left")
            ->field($field)
            ->where($map)
            ->paginate();
        return $data;
    }

    /**
     * 删除exam数据
     * @param $map 条件 id
     * @return $data
     */
    function deleteExam($map)
    {
        $res = $this->where($map)->delete();
        return $res;
    }


    /***
     * 获取证书
     * @param $data
     * @return mixed
     * @uses liuxin
     */
    public function getcertificate($data)
    {
        foreach ($data as $k => $v) {
            $result = $this
                ->field('id')
                ->where('uid',$v['uid'])
                ->where('isqualified','1')
                ->count();
            if ($result > 0) {
                $data[$k]['certificate'] = true;
            } else {
                $data[$k]['certificate'] = false;
            }
        }
       return $data;
    }

    /**
     * @param $map
     * @return array
     * 3是没有考试   2是有考试,但是没有通过  1是考试通过了,不用在考试了
     * @user 李海江 2018/8/1~下午12:07
     */
    function showTestAllow($map)
    {
        $order = 'create_time DESC';
        $list = $this->BaseFind($map, '', $order);
        if (isset($list) && !empty($list)) {
            if ($list['isqualified']) {
                return ['code' => 1];
            } else {
                return ['code' => 2, 'data' => $list];
            }
        } else {
            return ['code' => 3];
        }
    }
}
