<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\ Admin;
use app\common\model\ AuthGroup;
use think\Db;
use think\Request;
use think\validate;

/**
 * 后台首页控制器
 */
class User extends Adminbase
{

    //model
    private $Admin;
    private $AuthGroup;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Admin = new Admin();
        $this->AuthGroup = new AuthGroup();

    }

    /**
     * 用户列表
     */
    public function index()
    {
        //查询管理员用户 组合拥有角色名称
        $user_data = $this->Admin->selectUsersData();
        return view('', ['data' => $user_data]);
    }

    /**
     * 添加管理员
     */
    public function add_user()
    {
        if (Request::instance()->post()) {
            //添加管理员admin 和auth_group_access表
            $res = $this->Admin->addUserData();
            $this->success($res, 'index');
        } else {
            $data = $this->AuthGroup->selectAuthGroupData();
            return view('', ['data' => $data]);
        }
    }

    /**
     * 修改管理员
     */
    public function edit_user($id)
    {
        if (Request::instance()->post()) {
//            修改管理员
            $result = $this->Admin->editUserData($id);
            $this->success($result, 'Admin/User/index');
        } else {
            // 获取用户数据
            $user_data = $this->Admin->findUsersData($id);
            // 获取已加入用户组
            $group_data = $this->Admin->findGroupAccessData($id);
            // 全部用户组
            $data = $this->AuthGroup->selectAuthGroupData();
            return view('', ['data' => $data, 'user_data' => $user_data, 'group_data' => $group_data['group_id']]);
        }
    }

    /* 个人中心 */ /* 分开写是为了将权限更细化 */

    public function my_center()
    {
        $map = array(
            'username' => session('user')['username']
        );
        //获取当前管理员 查询admin数据
        $data = $this->Admin->selectUsersCenterData($map);
        return view('', ['all' => $data]);
    }


    protected $msg = [
        'email' => '邮箱格式不正确',
        'phone' => '手机号格式不正确',
    ];

    /* 修改个人资料 */
    public function change_msg()
    {
        if (Request::instance()->post()) {
          $validate = new Validate([
                 'email' => ['/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/'],
                 'phone' =>['/^1[34578]\d{9}$/'],
            ], $this->msg);
            // $username      = trim(input('post.username'));
            $data['email'] = trim(input('post.email'));
            $data['phone'] = trim(input('post.phone'));
           if (!$validate->check($data)) {
              $this->error($validate->getError(),"my_center");
          }
           //更新数据
            $result = $this->Admin->changeMsgData($data);
            if ($result) {
                // 操作成功
                // session('user', null);
                // $this->success('修改成功、前往登录页面', 'Home/Index/index');
                if(empty($post_password)){
                    $this->success('修改成功','my_center');
                }else{
                    session('user', null);
                    $this->success('修改成功,退出登录', 'Home/index/index');
                }
            } else {
                $this->error("您没有做任何修改","my_center");
            }
        }

        }


}
