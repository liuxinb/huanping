<?php
/**
 * Created by PhpStorm.
 * User: li_ha
 * Date: 2018/5/22
 * Time: 9:00
 */

namespace app\api\controller;

use think\Request;

/**
 * 购物车
 * Class Cart
 * @package app\api\controller
 */
class Cart extends Pro
{

    private $obj;
    private $uid;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->obj = model('Cart');
        $this->uid = model('User')->getUid(request()->header('token'));
    }

    /***
     * 获取我的购物车
     */
    public function getMyCart()
    {
        //获取uid
        $uid = $this->uid;
        //获取我的购物车
        $data = $this->obj->getMyCart($uid);
        //如果购物车为空返回
        if (empty($data)) result('40024');
        //输出购物车信息
        Pro::$message['20011']['data'] = $data;
        result('20011');
    }


    /**
     * 添加购物车
     * @param Request $request
     */
    public function addToCart(Request $request)
    {
        //过滤购物车想要的信息
        $data = create_mysqlData($request->post(), config('cart'));
        //get uid
        $data['uid'] = $this->uid;
        //获取商品单价
        $data['price'] = $this->obj->goodsPrice($data['goodsid']);
        //默认购买数量为1
        $data['count'] = 1;
        //判断商品是否存在
        $count = $this->obj->findGoods($data['goodsid']);
        //如果个数为0说明课程不存在
        if ($count == 0) result('40022');
        //执行添加操作 里面头获取价格 判断商品是否存在的操作
        if ($this->obj->addToCart($data)) result('20009');
    }

    /**
     * 移除购物车
     */
    public function removeCart()
    {
        $data['uid'] = $this->uid;
        $data['goodsid'] = request()->post('goodsid');
        //查找该视频是否存在
        $count = $this->obj->findGoods($data['goodsid']);
        //如果个数为0说明课程不存在
        if ($count == 0) result('40022');
        //如果视频存在执行删除操作
        $res = $this->obj->removeCart($data);
        if ($res) {
            result('20010');
        } else {
            result('40023');
        }
    }

    /***
     * 清空购物车
     */
    public function ClearCart()
    {
        $this->obj->clearCart();
    }

}