<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use think\Db;

class Index extends Adminbase
{
    public function index()
    {
//        dd(1);
        $version = Db::query('SELECT VERSION() AS ver');
        $config = [
            'url' => $_SERVER['HTTP_HOST'],
            'document_root' => $_SERVER['DOCUMENT_ROOT'],
            'server_os' => PHP_OS,
            'server_port' => $_SERVER['SERVER_PORT'],
            'server_soft' => $_SERVER['SERVER_SOFTWARE'],
            'php_version' => PHP_VERSION,
            // 'mysql_version'   => $version[0]['ver'],
            'max_upload_size' => ini_get('upload_max_filesize')
        ];
        $this->assign('config', $config);


        $user = session('user');
        $rules = Db::name("admin")
            ->alias('u')
            ->where("u.id=" . $user["id"])
            ->field('ag.rules')//c.title
            ->join('__AUTH_GROUP_ACCESS__ aga', 'u.id = aga.uid')
            ->join('__AUTH_GROUP__ ag', 'aga.group_id = ag.id')
            ->select()[0]["rules"];

        $ruleIdArray = explode(",", $rules);
        $map['id'] = array('in', $ruleIdArray);
        $menuArray = Db::name("Auth_rule")->where($map)->order("sort")->select();
        $parentArray = [];
        foreach ($menuArray as $item) {
            if ($item['pid'] == 0) {
                array_push($parentArray, $item);
            }
        }
        foreach ($parentArray as $key=>$parent) {
            $parentArray[$key]["childs"]=[];
            foreach ($menuArray as $item) {
                if ($item['pid'] == $parent["id"]) {
                    $parentArray[$key]["childs"][]=$item;
                }
            }
        }
        $this->assign("menuArray",$parentArray);
//        print_r($parentArray);
        return $this->fetch();
    }

    /**
     * 退出
     */
    // http://127.0.0.1/public/admin/index/logout
    public function logout()
    {
        session('user', null);
        $this->redirect('Home/Index/index');
    }
}
