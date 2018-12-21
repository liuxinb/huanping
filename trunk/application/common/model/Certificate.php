<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/6/14
 * Time: 上午10:53
 */
namespace app\common\model;

class Certificate extends Base
{
    /***
     * 证书管理Certificate数据
     * @param $map  条件查询
     * @return $data 返回结果
     */
    function showCertificateData(){
        $result= $this->alias('c')
            ->join("__USER_DETAIL__ u","c.uid=u.uid")
            ->field('c.id,c.url,c.create_time,c.url,c.certificate_id,c.name,u.name as username')
            ->paginate();
        return $result;
    }
}