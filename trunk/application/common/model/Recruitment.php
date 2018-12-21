<?php
/**
 * Created by PhpStorm.
 * User: liuxin
 * date: 2018/8/29
 * Time: 下午17:17
 */

namespace app\common\model;

class Recruitment extends Base
{
    public function getAllowDateAttr($time){
        return date('n月j日',$time);
    }

    public function dataAll($id, $field = '*')
    {
        return $this
            ->field($field)
            ->where('admin_id', $id)
            ->paginate();
    }

    public function dataDelete($id)
    {
        return $this
            ->where('id',$id)
            ->delete();
    }


    public function dataSave($data)
    {
        if ($data['id']) {
            return $this
                ->isUpdate(true)
                ->data($data, true)
                ->save();
        } else {
            return $this
                ->isUpdate(false)
                ->data($data, true)
                ->save();
        }
    }

    public function dataUpdate($data)
    {
        return $this->update($data);
    }
}