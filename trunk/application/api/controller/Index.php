<?php
/**
 * Created by PhpStorm.
 * User: li_ha
 * Date: 2018/5/15
 * Time: 19:24
 */

namespace app\api\controller;

use SendMsm\Msm;
use think\Request;

/***
 * 首页
 * Class Index
 * @package app\api\controller
 */
class Index extends Pro
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * 首页轮播图
     * @user 李海江 2018/7/18~下午12:29
     */
    public function advert()
    {
        $res = model('PlugList')->showAdvert;
        $res = addPath($res,'plug_pic','even');
        //控制app注册是否隐藏
        Pro::$message['20006']['registerIsHidden'] = true;
        Pro::$message['20006']['data'] = $res;
        result('20006');

    }

    /***
     * 发送短信验证码
     */
    public function sendMessage(Request $request)
    {
        $phone = $request->post('phone');
        $validata = Validate('User');
        if (!$validata->scene('sendmessage')->check(request()->post())) {
            result($validata->getError());
        }

        $res = Msm::sendMessage($phone);

        if (!$res['flag']) {
            die($res['message']);
        } else {
            result('20007');
        }
    }


    /***
     * 提交意见
     * @param Request $request
     */
    public function opinion(Request $request)
    {
        //获取token
        $token = $request->header('token');
        //获取数据
        $data = $request->post();
        $res = model('opinion')->opinion($data, $token);
        if ($res) {
            result('20018');
        }
    }

    /***
     * 检查版本号
     */
    public function version()
    {
        $data = request()->param();
        model('version')->checkversion($data);
    }

}
