<?php
/**
 * Created by PhpStorm.
 * User: li_ha
 * Date: 2018/5/9
 * Time: 11:38
 */

namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    /** 规则 **/
    protected $rule = [
        ['phone', 'require|length:11|/^1[345678][0-9]{9}$/', '40003|40004|40005'],
//        ['password', 'require|length:32', '40006|40007'],
        ['password', 'require', '40006'],
        ['idnumber', '/\d{18}/', '40010'],
        ['newpassword', 'require', '40016'],
//        ['newpassword', 'require|length:32', '40016|40007'],
        ['qqopenid', 'require', '40027'],
        ['wxopenid', 'require', '40027'],
        ['code', 'require', '40020'],
        ['content', 'require|length:10,600', '40033|40034'],
        ['star', 'require', '40035'],
        ['mid', 'require', '40025'],
    ];


    /** 场景设置 **/
    protected $scene = [
        //注册
        'register' => ['phone', 'password'],
        'register_detail' => ['idnumber'],
        //登录
        'login' => ['phone', 'password'],
        //修改密码
        'editUserPassword' => ['password', 'newpassword'],
        //修改用户信息
        'modifyUserMessage' => ['idnumber'],
        //忘记密码
        'forgetPassword' => ['password', 'password'],
        //发送验证码
        'sendmessage' => ['phone'],
        //qqopeind
        'qqlogin' => ['qqopenid'],
        //wxopenid
        'wxlogin' => ['wxopenid'],
        //绑定手机
        'bindphone' => ['phone', 'code'],
        //设置密码
        'setpwd' => ['password'],
        //意见
        'opinion' => ['content'],
        //评价
        'evaluation' => ['mid', 'content', 'star']
    ];


}