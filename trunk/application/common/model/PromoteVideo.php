<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/5
 * Time: 下午5:27
 */
namespace app\common\model;


class PromoteVideo extends Base {

    /**
     * 所有宣传视频数据
     */
    public function selectPromoteData($map){
        return $this->alias('p')
            ->join("__TEA_TEACHER__ t",'p.teacher_id=t.id' )
            ->field("p.*")
            ->field("t.name")
            ->where($map)
            ->paginate();
    }

    /**
     * 插入宣传视频数据
     * @param $data
     * @return bool
     */
    public function addPromoteData($data){
        return $this->insert($data);
    }

    /**
     * 删除宣传视频数据
     * @param $id 判断调价
     *
     */
    public function delPromoteData($id){
        return $this->where($id)->delete();
    }


    /***
     * 查找FlvMovie表字段
     * @param $id 数据集
     * @return data
     *  @data 2018/7/4 xuweiqi
     * */
    function findPromoteField($id){
        $request =  $this->find($id);
        return $request;
    }

    /***
     * 更新 FlvMovie表字段
     * @param $id 数据集
     * @return data
     *  @data 2018/7/4 xuweiqi
     * */
    function updatePromotePubData($id,$data){
        $request =  $this->where($id)->update($data);
        return $request;
    }

    /***
     * 获取所有宣传视频的vid
     * @param $map 条件
     * @return $data
     *  @data 2018/7/4 xuweiqi
     * */
    public function getPromoteField($map){
        $data=$this->field('id,title,vid')->where($map)->find();
        return $data;
    }





}