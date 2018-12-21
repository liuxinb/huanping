<?php

namespace app\common\model;


class Train extends Base
{
    /***
     * 添加培训计划，返回id
     * @param $data array
     */
    public function createTrainData($data)
    {
        $result = $this->save($data);
        return $result ? $this->id : false;
    }

    /***
     * 查询培训计划列表
     */
    public function selectTrainData($id)
    {
        return $this
            ->join('hp_flv_category flc', 'flc.id = hp_train.cid')
            ->field('flc.bag_img,hp_train.id,hp_train.cid,hp_train.name,hp_train.start_time,hp_train.end_time')
            ->where('hp_train.firm_id', $id)
            ->paginate();

    }
    function pagTrainData(){
        return $this->alias('t')
            ->join('__FLV_CATEGORY__ flc', 'flc.id = t.cid')
            ->field('flc.bag_img,t.id,t.cid,t.name,t.start_time,t.end_time,t.count,t.create_time')
            ->order("t.create_time desc")
            ->paginate();
    }

}