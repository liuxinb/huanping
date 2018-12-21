<?php
namespace app\common\model;

class Invoice extends Base
{
    /***
     * 发票管理Invoice数据
     * @param $map  条件查询
     * @return $data 删除结果
     */
    function showBillData(){
        $result= $this->paginate();
        return $result;
    }

    /***
     * 发票管理Invoice详情数据
     * @param $map  条件查询
     * @return $data 删除结果
     */
    function showBillDetailData($map){
        $result= $this->where($map)->find();
        return $result;
    }

}