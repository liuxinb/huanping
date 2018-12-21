<?php

namespace app\api\controller;

use think\Controller;

/**
 *  APP用户注册
 */
class Pro extends Controller
{
    /** 返回代码 提示信息 数据 **/
    public static $message = array(
        // 不成功系列
        '40001' => array('success' => false, 'code' => '40001', 'message' => '不允许非POST请求'),
        '40002' => array('success' => false, 'code' => '40002', 'message' => '用户不存在'),
        '40003' => array('success' => false, 'code' => '40003', 'message' => '请输入手机号码'),
        '40004' => array('success' => false, 'code' => '40004', 'message' => '请确认手机号码长度'),
        '40005' => array('success' => false, 'code' => '40005', 'message' => '手机号码不符合规则'),
        '40006' => array('success' => false, 'code' => '40006', 'message' => '请输入密码'),
        '40007' => array('success' => false, 'code' => '40007', 'message' => '密码非法请求'),
        '40008' => array('success' => false, 'code' => '40008', 'message' => '请输入身份验证TOKEN'),
        '40009' => array('success' => false, 'code' => '40009', 'message' => '登录状态失效，请重新登录'),
        '40010' => array('success' => false, 'code' => '40010', 'message' => '身份证格式不正确'),
        '40011' => array('success' => false, 'code' => '40011', 'message' => '密码错误'),
        //提交意见反馈
        '40033' => array('success' => false, 'code' => '40033', 'message' => '请输入您想说内容'),
        '40034' => array('success' => false, 'code' => '40034', 'message' => '内容在10到200个字间'),
        '20018' => array('success' => true, 'code' => '20018', 'message' => '反馈成功，谢谢您的意见'),
        '40027' => array('success' => false, 'code' => '40027', 'message' => 'OpenId请求错误'),
        '40030' => array('success' => false, 'code' => '40030', 'message' => 'OpenId不存在请绑定手机号'),
        //注册
        '40012' => array('success' => false, 'code' => '40012', 'message' => '您已注册过'),
        //退出登录
        '40013' => array('success' => false, 'code' => '40013', 'message' => '您已经退出，请勿重复操作'),
        //登录
        //修改密码
        '40016' => array('success' => false, 'code' => '40016', 'message' => '请输入您修改后的密码'),
        '40017' => array('success' => false, 'code' => '40017', 'message' => '原密码错误'),
        //登录
        '40018' => array('success' => false, 'code' => '40018', 'message' => '该账号已被冻结'),
        '40019' => array('success' => false, 'code' => '40019', 'message' => '操作失败，请重试'),
        '40020' => array('success' => false, 'code' => '40020', 'message' => '短信验证码错误'),
        //购物车
        '40021' => array('success' => false, 'code' => '40021', 'message' => '您的购物车已存在该课程'),
        '40022' => array('success' => false, 'code' => '40022', 'message' => '该视频不存在'),
        '40023' => array('success' => false, 'code' => '40023', 'message' => '请勿重复操作'),
        '40024' => array('success' => false, 'code' => '40024', 'message' => '购物车为空'),
        //id不能为空
        '40025' => array('success' => false, 'code' => '40025', 'message' => '请传递id'),
        //上传图片失败
        '40026' => array('success' => false, 'code' => '40026', 'message' => ''),
        //学习进度获取失败
        '40028' => array('success' => false, 'code' => '40028', 'message' => '您还未参加学习'),
        '40029' => array('success' => false, 'code' => '40029', 'message' => '暂无视频'),
        //评价
        '40035' => array('success' => false, 'code' => '40035', 'message' => '请给颗星吧'),
        '40044' => array('success' => false, 'code' => '40044', 'message' => '暂无评价'),
        '20019' => array('success' => true, 'code' => '20019', 'message' => '评价成功'),
        //该手机号已绑定其他账号
        '40031' => array('success' => false, 'code' => '40031', 'message' => '该手机号已绑定其他账号'),
        //您有密码
        '40032' => array('success' => false, 'code' => '40032', 'message' => '您过已设置密码'),
        // 成功系列
        '20001' => array('success' => true, 'code' => '20001', 'message' => '用户注册成功'),
        '20002' => array('success' => true, 'code' => '20002', 'message' => '修改成功'),
        '20003' => array('success' => true, 'code' => '20003', 'message' => '登录成功'),
        '20004' => array('success' => true, 'code' => '20004', 'message' => '退出成功'),
        '20005' => array('success' => true, 'code' => '20005', 'message' => '密码修改成功'),
        '20008' => array('success' => true, 'code' => '20008', 'message' => '密码找回成功，请牢记您的新密码'),
        '20016' => array('success' => true, 'code' => '20016', 'message' => '绑定成功'),
        '20017' => array('success' => true, 'code' => '20017', 'message' => '密码设置成功'),
        //验证码验证成功
        '20012' => array('success' => true, 'code' => '20012', 'message' => '验证码通过验证'),
        //轮播图成功
        '20006' => array('success' => true, 'code' => '20006', 'message' => '查询成功'),
        //短信验证码发送成功
        '20007' => array('success' => true, 'code' => '20007', 'message' => '发送成功'),
        //添加购物车成功
        '20009' => array('success' => true, 'code' => '20009', 'message' => '添加购物车成功'),
        '20010' => array('success' => true, 'code' => '20010', 'message' => '移除成功'),
        '20011' => array('success' => true, 'code' => '20011', 'message' => '查询成功'),
        //首页购买课程
        '20013' => array('success' => true, 'code' => '20013', 'message' => '查询成功'),
        //展示某一个视频包下的课程列表 注意这里的课程下还分学时
        '20014' => array('success' => true, 'code' => '20014', 'message' => '获取成功'),
        //上传图片
        '20015' => array('success' => true, 'code' => '20015', 'message' => '上传成功'),
        //获取通知
        '20020' => array('success' => true, 'code' => '20020', 'message' => '获取成功'),
        '40036' => array('success' => false, 'code' => '40036', 'message' => '暂无通知'),
        //视频观看进度
        '40037' => array('success' => false, 'code' => '40037', 'message' => '请传递观看进度'),
        //绑定第三方
        '40038' => array('success' => false, 'code' => '40038', 'message' => '您的第三方账号已绑定其他账号'),
        '40039' => array('success' => false, 'code' => '40039', 'message' => '该账号已绑定其他第三方账号'),
        '40047' => array('success' => false, 'code' => '40047', 'message' => '您已绑定成功,请勿重复操作'),

        '40040' => array('success' => false, 'code' => '40040', 'message' => '请输入APP版本号'),
        '40041' => array('success' => false, 'code' => '40040', 'message' => '请输入APP类型'),
        //版本更新
        '20021' => array('success' => true, 'code' => '20021', 'message' => '发现新版本，请更新'),
        '40042' => array('success' => false, 'code' => '40042', 'message' => '已是最新版本'),
        //证书
        //请通过考试再来打证
        '40043' => array('success' => false, 'code' => '40043', 'message' => '请先通过考试'),
        '20022' => array('success' => true, 'code' => '20022', 'message' => '获取成功'),
        //我的课程
        '40045' => array('success' => false, 'code' => '40045', 'message' => '您还未加入企业'),
        '40046' => array('success' => false, 'code' => '40046', 'message' => '您的企业未购买任何课程'),
        //考试
        '20023' => array('success' => true, 'code' => '20023', 'message' => '开始考试'),
        '20024' => array('success' => true, 'code' => '20024', 'message' => '考试结束'),
        '40048' => array('success' => false, 'code' => '40048', 'message' => '参加考试失败,请重试'),
        '40052' => array('success' => false, 'code' => '40052', 'message' => '请选择哪个测试'),
        '40049' => array('success' => false, 'code' => '40049', 'message' => '您近期无考试,请去参加考试吧'),
        '40050' => array('success' => false, 'code' => '40050', 'message' => '考试提交异常'),
        '40051' => array('success' => false, 'code' => '40051', 'message' => '您已经通过考试'),
        '40053' => array('success' => false, 'code' => '40053', 'message' => '请参加完学习再来考试吧~'),
        '40054' => array('success' => false, 'code' => '40054', 'message' => '课件陆续更新中，敬请期待！'),
        //获取招聘信息
        '20025' => array('success' => true, 'code'=>'20025', 'message'=> '获取成功'),
        '40055' => array('success' => false, 'code'=>'40055', 'message'=> '请选择需要查看的招聘信息'),
        //个人登录的没购买
        '40056' => array('success' => false, 'code'=>'40056', 'message'=> '暂时不能学习'),
        //暂未开放
        '40057' => array('success' => false, 'code'=>'40057', 'message'=> '功能暂未开放'),
        '40058' => array('success' => false, 'code'=>'40058', 'message'=> '账号不存在'),
    );

    /**
     * 公共函数 验证每次请求的token是否符合规则
     * 如果是GET请求不需要验证token
     * 如果是非GET需要验证token
     */
    public function __construct($request)
    {
        //不允许非post请求
        noIsPost($request);
        //不是所有的方法都验证token
        $url = strtolower(request()->controller() . '/' . request()->action());
        if (!in_array($url, config('noToken'))) {
            if (request()->header('token') == "") {
                result('40008');
            } else {
                $res = model('User')->hasOneCount('token', $request->header('token'));
                if (empty($res)) result('40009');
            }
        }
    }
}