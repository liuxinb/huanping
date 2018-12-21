<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use think\Db;
use think\Request;

/**
 * 
 * 首页管理
 */
class Welcome extends AdminBase {
    /* 首页 */
    public function index() {
        return $this->fetch();
    }
}
