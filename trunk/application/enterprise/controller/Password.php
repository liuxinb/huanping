<?php

namespace app\enterprise\controller;

use app\common\model\Firmsign;
use think\Controller;
use think\Request;
use \think\Validate;
use think\Session;
use app\common\model\EnterpriseOrder;

class Password extends Controller
{
    protected $msg = [
        'password.max' => '密码最多不能超过32个字符',
        'password.min' => '密码最少不能超过6个字符',
        'password.alphaDash' => '密码仅支持数字、字母',
        'ypassword.require' => '原始密码不能为空',
        'password.confirm' => '新密码和确认密码不一致'
    ];

    public function index()
    {
        return view('password/index');
    }

    //修改密码
    public function passwordgo(Request $request)
    {
        //验证规则
        $validate = new Validate([
            'ypassword' => 'require',
            'password' => 'require|alphaDash'
        ], $this->msg);
        $data = $request->param();
        if (!$validate->check($data)) {
            return $validate->getError();
        }
        if ($data['password'] == $data['ypassword']) {
            return '新密码不能和旧密码一致';
        }
        $user = Firmsign::get(session('rootId'));
        if (md5($data['ypassword']) == $user->password) {
            $user->password = md5($request->param('password'));
            $query = $user->save();
            return '密码重置成功，请重新登录！';
        } else {
            return '原始密码错误';
        }
    }
}