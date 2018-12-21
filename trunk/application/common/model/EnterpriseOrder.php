<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/6/21
 * Time: 上午10:28
 */

namespace app\common\model;

class EnterpriseOrder extends Base
{


    /**
     * 状态获取器
     * @param $value
     * @return mixed
     * @user 李海江 2018/9/12~下午7:23
     */
    public function getRoleAttr($value)
    {
        $status = [1=>'企业报名',2=>'院校报名',3=>'个人报名'];
        return $status[$value];
    }

    /***
     * 查询企业订单管理EnterpriseOrder数据 分页
     * @param $map  条件查询
     * @return $data 查询结果
     */
    function ShowEnterpriseData($map)
    {
        $data = $this::alias('e')
            ->join("__FLV_CATEGORY__ c", "e.category_id=c.id", "left")
            ->join("__FIRMSIGN__ f", "e.enterprise_id=f.id", "left")
            ->join("__RECORD__ r", "r.enterId=e.enterprise_id", "left")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->where($map)
            ->where('e.type=1')
            ->field("e.id,e.num,e.price,e.type,e.add_time,e.order_status,e.note,e.pay,e.count,r.firmname enterprise_name,c.title,f.email,r.province,r.city,r.county,r.registersite,r.addressphone,op.paytime,op.price as payprice,op.tradenum")
            ->order("e.add_time desc")
            ->paginate();
        return $data;
    }

    /***
     * 查询企业订单管理EnterpriseOrder数据 不分页,供下载订单 使用
     * @param $map  条件查询
     * @return $data 查询结果
     */
    function NotShowEnterpriseData($map)
    {
        $data = $this::alias('e')
            ->join("__FLV_CATEGORY__ c", "e.category_id=c.id", "left")
            ->join("__FIRMSIGN__ f", "e.enterprise_id=f.id", "left")
            ->join("__RECORD__ r", "r.enterId=e.enterprise_id", "left")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->field("e.id,e.num,e.price,e.add_time,e.order_status,e.note,e.pay,e.count,e.type,r.firmname enterprise_name,c.title,f.email,r.province,r.city,r.county,r.registersite,r.addressphone,op.paytime,op.price as payprice,op.tradenum")
            ->where($map)
            ->where("e.type=1")
            ->order("e.add_time desc")
            ->select();
        return $data;
    }

    /***
     * 订单管理EnterpriseOrder数据详情
     * @param $id  条件查询
     * @return $data 查询结果
     */
    function detailEnterpriseData($id)
    {
        $object = EnterpriseOrder::alias("e")
            ->join("__FLV_CATEGORY__ fc", "e.category_id=fc.id", "left")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->join("__FIRMSIGN__ f", "e.enterprise_id=f.id", "left")
            ->join("__RECORD__ r", "r.enterId=e.enterprise_id", "left")
            ->field('e.id,e.num,e.price,e.add_time,e.order_status,e.count,e.note,e.type,r.firmname enterprise_name,fc.title,op.pay,op.price as payprice,op.tradenum,op.paytime,f.email,r.province,r.city,r.county,r.registersite,r.addressphone')
            ->find($id);
        return $object;
    }



    /***
     * 订单EnterpriseOrder数据冻结
     * @param $map  条件查询
     */
    function delEnterpriseData($map)
    {
        $result = $this->where($map)->update(['order_status' => '0']);
        return $result;
    }


    /***
     * 获取EnterpriseOrder数据
     * @param $where  条件
     */
    public function getOrderListByFirmId($where)
    {
        return $this
            ->alias("e")
            ->join("__FLV_CATEGORY__ fc", "e.category_id=fc.id", "left")
            ->where($where)
            ->field("e.*,fc.*,e.id as eid")
            ->select();
    }
    /***
     * 获取EnterpriseOrder数据
     * @param $where  条件
     */
    public function getOrderListByFirmsId($where)
    {
        return $this
            ->alias("e")
            ->join("__FLV_CATEGORY__ fc", "e.category_id=fc.id", "left")
            ->join("__USER_DETAIL__ us", "e.enterprise_id=us.uid", "left")
            ->where($where)
            ->field("e.*,fc.*,e.id as eid,e.pid as epid,us.name as uname,us.idnumber,us.enterprise_id as usenterprise_id")
            ->select();
    }

    /***
     * 获取EnterpriseOrder数据
     * @param $where  条件
     */
    public function getOrderJoinByFirmId($where)
    {
        return $this
            ->alias("e")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->join("__FLV_CATEGORY__ fc", "e.category_id=fc.id", "left")
            ->join("__INVOICE__ in", "e.num=in.num", "left")
            ->where($where)
            ->field("e.*,op.*,fc.*,in.pdf_url,in.num as order_num,e.price as price,op.price as op_price,e.update_time eupdate_time,e.pay epay")
            ->select();
    }

    /***
     * 获取EnterpriseOrder数据
     * @param $where  条件
     */
    public function getOrderJoinByFirmsId($where)
    {
        return $this
            ->alias("e")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->join("__FLV_CATEGORY__ fc", "e.category_id=fc.id", "left")
            ->join("__INVOICE__ in", "e.num=in.num", "left")
            ->join("__USER_DETAIL__ us", "e.enterprise_id=us.uid", "left")
            ->where($where)
            ->field("e.*,op.*,fc.*,in.pdf_url,in.num as order_num,e.price as price,op.price as op_price,us.name as uname,us.idnumber,e.update_time pay_time,e.pid epid,e.pay epay")
            ->select();
    }
    /***
     * 获取EnterpriseOrder数据
     * @param $where  条件
     */
    public function selDetail($where)
    {
        return $this
            ->alias("e")
            ->join("__USER_DETAIL__ us", "e.enterprise_id=us.uid", "left")
            ->where($where)
            ->field("e.*,us.name uname,us.idnumber,e.num as enum")
            ->select();
    }

    /***
     * 获取EnterpriseOrder数据
     * @param $where  条件
     * @param $limit  个数
     */
    public function getOrderListByFirmNum($where, $limit = '')
    {
//        $this->table = empty($table)?$this->table:$table;
        $sel = empty($limit) ? "select" : "find";
        return $this
            ->where($where)
            ->$sel();
    }


    /***
     * 修改EnterpriseOrder数据
     * @param $where  条件
     * @param $save   修改数据
     */
    public function saveOrderListByFirmNum($where, $save)
    {
        return $this
            ->where($where)
            ->update($save);
    }

    /***
     * 查找EnterpriseOrder数据
     * @param $id   传入的id
     * @data 2018/7/4 xuweiqi
     */
    function findOrderData($id)
    {
        $data = $this->where($id)->find();
        return $data;
    }

    /***
     * EnterpriseOrder数据
     * @param $where  条件
     * @param $str 0多条 !0单条
     * @param $order    排序
     */
    public function getSignupList($where, $str = 0, $order = '')
    {
//        $this->table = empty($table)?$this->table:$table;
        $sel = ($str == 0) ? "select" : "find";
        $order = empty($order) ? "" : $order;
        return $this
            ->where($where)
            ->order($order)
            ->$sel();
    }

    //根据条件获取订单表数据
    public function addData($data)
    {
        return $this
            ->save($data);
    }

    /***
     * 查找EnterpriseOrder数据 join 详情表
     * @param $where  条件
     */
    public function getJoinRecordlist($where)
    {
        return $this
            ->alias('e')
            ->join("__RECORD__ r", "e.enterprise_id=r.enterId", "inner")
            ->where($where)
            ->find();
    }

    /***
     * 企业线下支付
     * @param $where  条件
     */
    public function findJoinRecordlist($where)
    {
        return $this
            ->where($where)
            ->find();
    }

    /***
     * 获取订单支付状态
     * @param $id
     * return 未支付返回false 已支付返回课程包id
     */
    public function getOrderpaystatus($id)
    {
        $orderStatus = $this
            ->where('enterprise_id', $id)
            ->where('order_status',1)
            ->select();
        if ($orderStatus) {
            $cidString = '';
            foreach ($orderStatus as $k=>$v) {
                $cidString .= $v->category_id.',';
            }
            return $cidString;
        }
        return false;
    }

    /***
     * 查询订单管理个人数据 分页
     * @param $map  条件查询
     * @return $data 查询结果
     */
    function ShowPeoSchoolData($map)
    {
        $data = $this::alias('e')
            ->join("__FLV_CATEGORY__ c", "e.category_id=c.id", "left")
            ->join("__USER__ u", "u.id=e.enterprise_id", "left")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->where($map)
            ->where(' e.type=3')
            ->field("u.phone,e.id,e.num,e.enterprise_name,e.price,e.update_time,e.add_time,e.order_status,e.pid,e.note,e.pay,e.count,c.title,e.type,op.paytime,op.price as payprice,op.tradenum")
            ->order("e.add_time desc")
            ->paginate();
        return $data;
    }


    /***
     * 订单管理个人EnterpriseOrder数据详情
     * @param $id  条件查询
     * @return $data 查询结果
     */
    function detailPeoSchoolData($id)
    {
        $object = EnterpriseOrder::alias("e")
            ->join("__FLV_CATEGORY__ fc", "e.category_id=fc.id", "left")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->join("__USER__ u", "u.id=e.enterprise_id", "left")
            ->field('u.phone,e.id,e.num,e.enterprise_name,e.update_time,e.price,e.add_time,e.order_status,e.count,e.note,e.type,fc.title,op.pay,op.price as payprice,op.tradenum,op.paytime')
            ->find($id);
        return $object;
    }


    /***
     * 查询个人订单管理EnterpriseOrder数据 不分页,供下载订单 使用
     * @param $map  条件查询
     * @return $data 查询结果
     */
    function NotShowProSchoolData($map)
    {
        $data = $this::alias('e')
            ->join("__FLV_CATEGORY__ c", "e.category_id=c.id", "left")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->join("__USER__ u", "u.id=e.enterprise_id", "left")
            ->field("u.phone,e.id,e.num,e.price,e.enterprise_name,e.add_time,e.order_status,e.note,e.pay,e.count,e.type,c.title,op.paytime,op.price as payprice,op.tradenum")
            ->where($map)
            ->where("e.type=3")
            ->order("e.add_time desc")
            ->select();
        return $data;
    }


    /***
     * 查询订单管理院校和个人数据 分页
     * @param $map  条件查询
     * @return $data 查询结果
     */
    function ShowSchoolData($map)
    {
        $data = $this::alias('e')
            ->join("__FLV_CATEGORY__ c", "e.category_id=c.id", "left")
            ->join("__FIRMSIGN__ f", "f.id=e.enterprise_id", "left")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->where($map)
            ->where('e.type=2')
            ->field("f.email,e.id,e.num,e.enterprise_name,e.price,e.add_time,e.order_status,e.note,e.pay,e.count,c.title,e.type,op.paytime,op.price as payprice,op.tradenum")
            ->order("e.add_time desc")
            ->paginate();
        return $data;
    }

    /***
     * 订单管理院校EnterpriseOrder数据详情
     * @param $id  条件查询
     * @return $data 查询结果
     */
    function detailSchoolData($id)
    {
        $object = EnterpriseOrder::alias("e")
            ->join("__FLV_CATEGORY__ fc", "e.category_id=fc.id", "left")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->join("__FIRMSIGN__ f", "f.id=e.enterprise_id", "left")
//            ->join("__COLLEGE_RECORD__ cr", "e.academy_id=cr.id", "left")
            ->field('f.email,e.id,e.num,e.enterprise_name,e.price,e.add_time,e.order_status,e.count,e.note,e.type,fc.title,op.pay,op.price as payprice,op.tradenum,op.paytime')
            ->find($id);
        return $object;
    }


    /***
     * 查询个人院校订单管理EnterpriseOrder数据 不分页,供下载订单 使用
     * @param $map  条件查询
     * @return $data 查询结果
     */
    function NotShowSchoolData($map)
    {
        $data = $this::alias('e')
            ->join("__FLV_CATEGORY__ c", "e.category_id=c.id", "left")
            ->join("__ORDER_PAY__ op", "e.num=op.ordernum", "left")
            ->join("__FIRMSIGN__ f", "f.id=e.enterprise_id", "left")
            ->field("f.email,e.id,e.num,e.price,e.enterprise_name,e.add_time,e.order_status,e.note,e.pay,e.count,e.type,c.title,op.paytime,op.price as payprice,op.tradenum")
            ->where($map)
            ->where("e.type=2")
            ->order("e.add_time desc")
            ->select();
        return $data;
    }


}