<?php

namespace app\common\model;

use think\db;

class Videoplayer extends Base
{
    /***
     * 获取该企业学习播放器进度
     * @param $id
     * @param $traindata
     */
    public function getVideoplan($id,$traindata)
    {
        $result = $this
            ->field('id')
            ->where('enterprise_id',$id)
            ->count();
        if ($result <= 0) {
            foreach ($traindata as $k=>$v) {
                $traindata[$k]['url'] = 0;
                $traindata[$k]['videoTime'] = 0;
            }
        } else {
            foreach ($traindata as $k=>$v) {
                $traindata[$k]['url'] = $this->field('movie_url')->where('train_id',$v['id'])->find()['movie_url'];
                $traindata[$k]['videoTime'] = $this->field('movie_paln')->where('train_id',$v['id'])->find()['movie_paln'];
            }
        }
        return $traindata;
    }

    /***
     * 查询播放记录，没有记录添加，有记录修改
     * @param $data
     */
    public function addTrainvideoplan($data)
    {
        $result = $this
            ->field('id')
            ->where('enterprise_id',$data['enterprise_id'])
            ->where('train_id',$data['train_id'])
            ->count();
        if ($result) {
            $arr = [
                'id' => $this
                    ->field('id')
                    ->where('enterprise_id',$data['enterprise_id'])
                    ->where('train_id',$data['train_id'])
                    ->find()['id'],
                'movie_url' => $data['movie_url'],
                'movie_paln' => $data['movie_paln']
            ];
            $dataResult = $this->update($arr);
        } else {
            $dataResult = $this->save($data);
        }
        return $dataResult;
    }
}
