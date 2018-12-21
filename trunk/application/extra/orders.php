<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/7/10
 * Time: 9:51
 * 支付宝配置信息
 */

return  [
    'service' => "create_direct_pay_by_user",
    'partner' => "2088131493609365",  //合作身份者id，以2088开头的16位纯数字
    'sellerEmail' => "ali-huanping@etlchina.net",  //收款支付宝账号
    'key' => "gicqa7xsmpgjfomuvtjgpw8et7izyr4q",  //安全检验码，以数字和字母组成的32位字符
    'transport' => "http",  //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    'paymentType' => "1",  // 支付类型

    'notifyUrl' => "http://www.elink.etledu.com/notify", // 服务器异步通知页面路径
    'returnUrl' => "http://www.elink.etledu.com/notify", // 页面跳转同步通知页面路径
    'wxnotifyUrl' => "http://www.elink.etledu.com/wxNotify", // 微信回调
    'showUrl' => "",  // 商品展示地址 可选
    'antiPhishingKey' => "", // 防钓鱼时间戳  若要使用请调用类文件submit中的query_timestamp函数
    'exterInvokeIp' => "", // 客户端的IP地址
    'InputCharset' => 'utf-8', // 字符编码格式

];