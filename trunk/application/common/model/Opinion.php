<?php
namespace app\common\model;

/**
 * Class Opinion
 * @package app\common\model
 */
class Opinion extends Base
{
    //自动写入时间
    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = false;

    /**
     * @param $data
     * @param $token
     * @return false|int
     */
    function opinion($data, $token)
    {
        $validata = Validate('User');
        if (!$validata->scene('opinion')->check($data)) {
            result($validata->getError());
        }
        $uid = model('user')->getUid($token);
        unset($data['id']);
        $data['userid'] = $uid;
        return $this->save($data);
    }

    /**
 * 查询Opinion数据
 * @return data
 */
    function selectOpinionData()
    {
        $data=  $this
            ->alias('o')
            ->join('__USER__ u','o.userid=u.id','left')
            ->field('u.phone,o.id,o.content,o.create_time')
            ->paginate();
        return $data;
    }

    function sel(){
//        return $this->BaseWithSelect('','','','','');
        return $this->hasOne('UserDetail','uid','udid');
    }

    /**
     * 删除Opinion数据
     * $param $id 条件 id
     * @return data
     * @date 2018/7/5 xuweiqi
     */
    function delOpinionData($id)
    {
        $data= $this->where($id)->delete();
        return $data;
    }


}