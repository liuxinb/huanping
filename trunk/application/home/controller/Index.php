<?php

namespace app\home\controller;

use app\common\controller\Homebase;
use app\common\model\Admin;
use think\Request;
use think\Db;

/**
 * Login Controller
 */
class Index extends Homebase {

    //model
    private $Admin;
    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Admin =new Admin();
    }

    /**
     * 首页
     */
    public function index() {
        if (Request::instance()->isPost()) {
            // 一个简单的登录 组合where数组条件
            $map = input('post.');
            $map['password'] = md5($map['password']);
            if(!captcha_check($map['verifyCode'])) {
                // 校验失败
               return $this->error('验证码不正确',"home/index/index");
            }
            unset($map['verifyCode']);
            $data =$this->Admin->where($map)->find();
            if (empty($data)) {
                 return $this->error('账号或密码错误',"home/index/index");
            } else {
                $sdata = [
                    'id' => $data['id'],
                    'username' => $data['username'],
                    'avatar' => $data['avatar'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ];
                session('user', $sdata);
                $this->redirect('Admin/Index/index');
            }
        } else {
            return $this->fetch();
        }
    }

    /**
     * 退出
     */
    // http://127.0.0.1/public/home/index/logout
    public function logout() {
        session('user', null);
        $this->redirect('Home/Index/index');
    }

}
