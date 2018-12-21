<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/7/12
 * Time: 18:28
 */
namespace app\common\model;

use think\Db;

class FirmHope extends Base
{
    public function addOne($data)
    {
        return $this
            ->save($data);
    }
}