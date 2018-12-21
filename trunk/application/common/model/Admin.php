<?php

namespace app\common\model;

use think\Db;
use think\Validate;

class Admin extends Base
{


    /***
     * 根据条件删除教师数据
     * @param $map 条件 id
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    public function selectUsersData()
    {
        $data = Db::name('auth_group_access')
            ->alias('aga')
            ->field('u.id,u.username,u.email,aga.group_id,ag.title')
            ->join('__ADMIN__ u', 'aga.uid=u.id', 'RIGHT')
            ->join('__AUTH_GROUP__ ag', 'aga.group_id=ag.id', 'LEFT')
            ->select();
        $first = $data[0];
        $first['title'] = array();
        $user_data[$first['id']] = $first;
        // 组合数组
        foreach ($data as $k => $v) {
            foreach ($user_data as $m => $n) {
                $uids = array_map(function ($a) {
                    return $a['id'];
                }, $user_data);
                if (!in_array($v['id'], $uids)) {
                    $v['title'] = array();
                    $user_data[$v['id']] = $v;
                }
            }
        }
        // 组合管理员title数组
        foreach ($user_data as $k => $v) {
            foreach ($data as $m => $n) {
                if ($n['id'] == $k) {
                    $user_data[$k]['title'][] = $n['title'];
                }
            }
            $user_data[$k]['title'] = implode('、', $user_data[$k]['title']);
        }
        return $user_data;
    }


    /***
     * 添加管理员
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    public function addUserData()
    {
        $data = input('post.');
        $validate = new Validate([
            'phone' =>['/^1[34578]\d{9}$/'],
            'email' => ['/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/'],
        ], $this->msg);
        $datas['phone'] = trim(input('post.phone'));
        $datas['email'] = trim(input('post.email'));
        if (!$validate->check($datas)) {
            return $validate->getError();
        }
        $usernameFind=$this->where('username',$data['username'])->find();
        if(!empty($usernameFind)){
            return '不能重复注册!';
        }
        // dump($data);
        $userdata = [
            'username' => $data['username'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'email' => $data['email'],
            'status' => $data['status'],
        ];

        $result = $this->insert($userdata);
        $datagroup = $this->where(['username' => $data['username'], 'email' => $data['email']])->find();
        if ($result) {
            if (!empty($data['group_ids'])) {
                foreach ($data['group_ids'] as $k => $v) {
                    $group = array(
                        'uid' => $datagroup['id'],
                        'group_id' => $v
                    );
                    Db::name('auth_group_access')->insert($group);
                }
            }
           // 操作成功
           return '添加成功';
        } else {
           return '修改失败';
        }
    }

    /***
     * 修改管理员
     * @param $id 条件
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    public function editUserData($id)
    {
            $data = input('post.');
          //验证邮箱手机号
            $validate = new Validate([
                'phone' =>['/^1[34578]\d{9}$/'],
                'email' => ['/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/'],
            ], $this->msg);
            // $username      = trim(input('post.username'));
            $datavali['phone'] = trim(input('post.phone'));
            $datavali['email'] = trim(input('post.email'));
            if (!$validate->check($datavali)) {
               return $validate->getError();
            }
            trace("NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN");
            trace($data);
            trace("NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN");
            Db::name('auth_group_access')->where(array('uid' => $id))->delete();
            if (!empty($data['group_ids'])) {
                foreach ($data['group_ids'] as $k => $v) {
                    $group = [
                        'uid' => $id,
                        'group_id' => $v
                    ];
                    Db::name('auth_group_access')->insert($group);
                }
            }
            // $data=array_filter($data);
            // 如果修改密码则md5
            // p($data);die;
            // if ($data['password']!=null) {
            $userup['password'] = md5($data['password']);
            // }
            $userup = [
                'password' => md5($data['password']),
                'username' => $data['username'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'status' => $data['status'],
            ];
            $results =$this->where(['id' => $id])->find();
            $result =$this->where(['id' => $id])->update($userup);
//            trace("****************************************************************************");
//            trace($userup);
//            trace($results);
//            trace($result);
//            trace("****************************************************************************");
            if($result){
                return '修改成功';
            }else{
                return '您未修改数据';
            }
    }

    /***
     * 查找管理员数据
     * @param $id 条件
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    public function findUsersData($id)
    {
        $result=$this->find($id);
        return $result;
    }

    /***
     * 查找权限组数据
     * @param $id 条件
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    public function findGroupAccessData($id)
    {
        $group_data = Db::name('auth_group_access')
            ->where(array('uid' => $id))
            ->find();
        return $group_data;
    }

    /***
     * 查找权限组数据
     * @param $id 条件
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    public function selectUsersCenterData($map)
    {
        $data=$this->where($map)->select();
        return $data;
    }

    protected $msg = [
        'email' => '邮箱格式不正确',
        'phone' => '手机号格式不正确',
    ];

    /***
     * change_msg 修改个人资料
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    public function changeMsgData($data)
    {
        $map = array(
            'username' => session('user')['username']
        );
        $post_password = input('post.password');
        if (!empty($post_password)) {
            $data['password'] = md5(input('post.password'));
        }
        $result = $this->where($map)->update($data);
        return $result;

    }







}