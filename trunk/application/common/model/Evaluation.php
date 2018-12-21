<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/6/11
 * Time: 上午9:07
 */
namespace app\common\model;

class Evaluation extends Base
{

    public function ShowEvaluateData(){
        $field='e.id,e.create_time,e.star,e.content,u.name,f.title';
        $map='e.create_time desc';

        $data= $this->alias('e')
            ->join("__USER_DETAIL__ u",'e.uid=u.uid','left')
            ->join("__FLV_MOVIE__ f",'e.mid=f.id','left')
            ->field($field)
            ->order($map)
            ->paginate();
        return $data;
    }

    public function delEvaluateData($id){
        $data=$this->where($id)->delete();
        return $data;
    }
}