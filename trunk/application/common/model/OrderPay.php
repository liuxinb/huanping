<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/7/10
 * Time: 14:35
 */

namespace app\common\model;

class OrderPay extends Base
{
    public function addOrderListByFirmNum($arrData){
//        $this->table = empty($table)?$this->table:$table;
        return $this
            ->save($arrData);
    }


    /***
     * 添加OrderPay数据
     * @param $save   修改数据
     */
    public function addOrderPayData($data)
    {
        return $this
            ->save($data);
    }
}