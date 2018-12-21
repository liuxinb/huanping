<?php

namespace app\common\controller;

use app\common\controller\Base;
use think\Request;

/**
 * admin 基类控制器
 */
class AdminBase extends Base
{
    /**
     * 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();
        if (session('user')) {
            if (session('user')['username'] != "sadmin") {
                $auth = new \think\Auth();
                $request = Request::instance();
                $m = $request->module();
                $c = $request->controller();
                $a = $request->action();
                $rule_name = $m . '/' . $c . '/' . $a;

                $result = $auth->check($rule_name, session('user')['id']);
                if (!$result) {
                    $this->error('您没有权限访问','home/index/index');
                }
            }
        }else{
            $this->redirect("/home");
        }
    }

    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        if (array_key_exists("layout", $vars)) {
            return $this->view->fetch($template, $vars, $replace, $config);
        } else {
            $this->assign("childUrl", $template);
            return $this->view->fetch($template, $vars, $replace, $config);
        }
    }
}

