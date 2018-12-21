<?php
/**
 * Created by PhpStorm.
 * User: li_ha
 * Date: 2018/5/16
 * Time: 14:05
 */

namespace app\common\model;

use app\common\model\AdminOrder;
use think\Model;

class UserDetail extends Base
{
    //自动写入时间
    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = false;


    /**
     * 注册用户
     * @param array $data 入库信息
     * @return bool
     * @user lihaijiang  2018/7/9 下午6:29
     */
    function add($data)
    {
        /**
         * POST的数据
         * @param tel       电话号码(必填)
         * @param password  密码(必填)
         * @param teoken    token(必填)
         * @param model     手机类别 android ios(选填 默认为android)
         * @param wechat    微信号(选填)
         * @param qq        QQ号(选填)
         */
        //数据过滤
        $validate = Validate('User');
        if (!$validate->scene('register_detail')->check($data)) {
            result((string)$validate->getError());
        }
        if ($this->save($data)) {
            return true;
        }
    }

    /***
     * 修改用户信息
     * @param $uid 用户uid
     * @return bool 修改结果
     */
    function modifyUserMessage($uid, $data)
    {
        $res = $this->where('uid', $uid)
            ->update($data);
        return $res;
    }

    /***
     * 查询员工管理数据
     * @param $map   查询条件
     * @return $data 查询结果
     */
    function ShowUserDetailDate($map)
    {
        $data = $this
            ->alias("ud")
            ->join("__USER__ u", "ud.uid=u.id", "left")
            ->join("__RECORD__ r", "r.enterId=ud.enterprise_Id", "left")
            ->field("ud.id,ud.name as sname,ud.uid,ud.idnumber,ud.idnumber as showidnumber,u.phone,u.phone as showphone,u.password,u.status,r.firmname,ud.create_time")
            ->where($map)
            ->paginate();
        return $data;
    }

    /***
     * 遍历数据
     * @param $id
     * @return $data 删除结果
     */
    function hiddenPhone($data){
        $res=[];
        foreach ($data as $k=>$v){
                $tmp['id']=$v->id;
                $tmp['name']=$v->sname;
                $tmp['uid']=$v->uid;
                $tmp['idnumber']=self::yc_phone($v->idnumber,7,8); //隐藏身份证号中间几位
                $tmp['showidnumber']=$v->showidnumber;//完整身份证号
                $tmp['phone']=self::yc_phone($v->phone,3,4); //隐藏手机号中间几位
                $tmp['showphone']=$v->showphone;   //完整手机号
                $tmp['password']=$v->password;
                $tmp['status']=$v->status;
                $tmp['firmname']=$v->firmname;
                $tmp['create_time']=$v->create_time;
                $res[] = $tmp;
        }
        return $res;
    }


    /***
     * 手机号,身份证号隐藏中间几位
     * @param $str 手机号和身份证号
     * @param $start 字符串开始数
     * @param $length 字符串要隐藏的个数
     * @return $resstr
     */
    function yc_phone($str,$start,$length){
        $str=$str;
        $resstr=substr_replace($str,'****',$start,$length);
        return $resstr;
    }

    /***
     * 删除员工管理UserDetail数据
     * @param $id
     * @return $data 删除结果
     */
    function DelUsermentData($id)
    {
        $data = $this->where(['uid' => $id])->delete();
        return $data;
    }

    /***
     * 删除员工管理User数据
     * @param $table
     *  *@param $id
     * @return $data 删除结果
     */
    function DelUserData($id)
    {
        $data = model('User')->where(['id' => $id])->delete();;
        return $data;
    }

    /**
     * 验证用户详情数据唯一，真实性
     * @param string     data 要验证的数据
     * @param string    field 数据字段
     * @param int       userid 用户基本表id
     * @return bool
     * @user liuxin     2018/7/9
     */
    public function onlyUserdetail($data, $field, $userid = '')
    {
        if ($userid) {
            return $this->where($field, $data)
                ->where('uid', 'neq', $userid)
                ->find();
        } else {
            return $this->where($field, $data)
                ->find();
        }
    }

    /***
     * 用户基本表
     * @return object
     * @user liuxin 2018/7/9
     */
    public function xueyuan()
    {
        return $this->hasOne('Xueyuan', 'uid', 'uid');
    }
}