<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;


//企业申请

route::rule('/', 'index/index/index');//域名输入默认企业登录地址
Route::rule('shop', 'index/index/shop');
Route::rule('cooper', 'index/index/cooper');
Route::rule('questionnaire', 'index/index/questionnaire');//提交问卷
Route::rule('help', 'index/index/help');//帮助

Route::rule('register', 'index/login/register');
Route::rule('login', 'index/login/login');
Route::rule('loginOut', 'index/login/loginOut');//退出登录


/**企业后台管理**/
Route::rule('password', 'enterprise/password/index');//修改密码
Route::rule('passwordgo', 'enterprise/password/passwordgo');//密码
//公共部分
Route::rule('qiyes', 'enterprise/menu/index');
//企业管理中心
Route::rule('manage', 'enterprise/menu/show');
//院校管理中心
Route::rule('academyManage', 'enterprise/menu/academy');
//报名
Route::rule('signup', 'enterprise/signup/index');
//批量报名
Route::rule('batch', 'enterprise/signup/batch');
Route::rule('paybatch', 'enterprise/orders/paybatch'); //批量缴费
Route::rule('allbatch', 'enterprise/signup/allbatch');//报名学生页面
Route::rule('addbatch', 'enterprise/orders/addbatch');//添加报名学生
Route::rule('selbatch', 'enterprise/orders/selbatch');//查询报名学生
Route::rule('totalorder', 'enterprise/orders/totalorder');//生成总订单
Route::rule('payment', 'enterprise/orders/payment');//线下支付
Route::rule('onlinepay', 'enterprise/orders/onlinepay');//线上支付
Route::rule('selinvoice', 'enterprise/invoice/selinvoice');//线上支付
//生成订单并支付
Route::rule('addOrder', 'enterprise/orders/addOrder');
//微信支付
Route::rule('wxPay', 'enterprise/Wxnative/index');
//生成二维码
Route::rule('qrcode', 'enterprise/orders/qrcode');
//微信验证是否支付
Route::rule('wxNotifyUrl', 'enterprise/orders/wxNotifyUrl');
//回调
//Route::rule('wxNotify', 'enterprise/orders/wxNotify');
Route::rule('wxNotify', 'api/callback/wxNotify');
//支付宝同步回调
//Route::rule('notify', 'enterprise/orders/notify');
Route::rule('notify', 'api/callback/notify');
//支付宝异步回调
//Route::rule('res', 'enterprise/orders/res');
Route::rule('res', 'api/callback/res');
//我的档案
Route::rule('recordadd', 'enterprise/record/index');
Route::rule('record', 'enterprise/record/update');
Route::rule('recordAcademy', 'enterprise/College/index');

//企业订单
Route::rule('orders', 'enterprise/orders/index');
//发票管理
Route::rule('invoice', 'enterprise/invoice/index');
//抬头
Route::rule('drawing', 'enterprise/invoice/drawing');
//员工管理
Route::rule('users', 'enterprise/users/index');
//学习管理
Route::rule('student', 'enterprise/student/index');
//课程展示
Route::rule('course', 'enterprise/course/index');
//集中培训
Route::rule('train', 'enterprise/train/index');
Route::rule('trainAdd', 'enterprise/train/add');//添加课程
Route::rule('trainStudylist', 'enterprise/train/studyList');//学员列表
Route::rule('trainVideolist', 'enterprise/train/videoList');//视频列表
Route::rule('trainVideoplanSave', 'enterprise/train/saveVideoplan');//视频列表
//招聘管理
Route::rule('recruit', 'enterprise/recruit/index');
//院校信息
Route::rule('academyInfo', 'index/academy/index');

Route::rule('close', 'index/index/close');

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]' => [
        ':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
