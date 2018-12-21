<?php
namespace app\common\model;

/**
* admin_firmsign_survey 表模型
*/
class FirmsignSurvey extends Base
{
    /***
     * 获取Firmsign_survey 所有数据
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    function selectFirmsignSurveyData()
    {
        $data=$this->order('create_time desc')->paginate();
        return $data;
    }
    /***
     * 根据条件删除Firmsign_survey 数据
     * @param $map 条件 id
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    function deleteFirmsignSurveyData($map)
    {
        $data=$this->where($map)->delete();
        return $data;
    }

    /***
     * 添加Firmsign_survey 数据
     * @param $data 数据
     * @return bool
     * @data 2018/7/3 xuweiqi
     */
    function insertFirmsignSurveyData($data)
    {
        $data=$this->insert($data);
        return $data;
    }

    /***
     * 根据条件删除Firmsign_survey 数据
     * @param $data 条件 id
     * @return bool
     * @data 2018/7/3 xuweiqi
     */
    function updateFirmsignSurveyData($data)
    {
        $data=$this->update($data);
        return $data;
    }


    public function getList($where='',$field="*",$find='select')
    {
        return $this
            ->where($where)
            ->field($field)
            ->$find();
    }

    public function saveOne($id,$field){
        return $this
            ->where($id)
            ->setInc($field,1);
    }






}