<?php
namespace app\common\controller;

use app\common\controller\Base;
use think\Request;
use think\Cookie;
/**
 * admin 基类控制器
 */
class EnterBase extends Base{
    /*
      * 普通角色 admin_glyadmins
      * 超级管理员 admin_firmsign
     * */
    public function _initialize(){
        parent::_initialize();

        //验证登录状态
        if (!session('rootId')) {
            return $this->redirect('/');
        }

        //权限验证，验证通过或拦截
         $userId = session('rootId');
        if (empty($userId)){
            $userCookie = Cookie::get('rootId');
            if (empty($userCookie)){
                return "<script>alert('请先登录');parent.location.href='/index'; </script>";
            }
        }
    }

    //获取当前登录角色信息
    public function role()
    {
        $adminUserinfo = session('adminRole');
        return $adminUserinfo->type;
    }

}

