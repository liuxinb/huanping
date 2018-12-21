<?php

namespace app\common\model;


/**
 * 消息管理模型 admin_message
 */
class Message extends Base
{


    public function send() {
        $data = $this->create();
        if (!$data) {  //数据对象创建错误
            return false;
        }
        $data['uid'] = 0;
        if ($data['type'] != 1) {
            $list = model("Admin")->where('status=1')->field('id')->select();
            foreach ($list as $v) {
                $data['to_uid'] = $v['id'];
                $rs = $this->add($data);
            }
        } else {
            $map['username'] = input("post.username");
            if (empty($map['username'])) {
                $this->error = '请输入要接收消息的用户名！';
                return false;
            }
            $uid = model("Admin")->where($map)->field('id')->find();
            $data['to_uid'] = $uid['id'];
            if (empty($data['to_uid'])) {
                $this->error = '对不起没有找到该用户！';
                return false;
            }
            $rs = $this->add($data);
        }
        //记录行为
//        action_log('add_message', 'prize', $data['type']);
        return $rs;
    }

    /***
     * 查询Message和user detail中字段
     * @return $data
     * @date 2018/7/5 xuweiqi
     */
    function selectIndexMessageData($map){
        $list = $this
            ->alias('m')
            ->join('__USER_DETAIL__ u', 'u.uid=m.to_uid',"left")
            ->field('u.name as to_uid,m.id,m.title,m.content,m.create_time,m.type,m.status')
            ->where($map)
            ->order('m.type desc')
            ->paginate();
        return $list;
    }

    /***
     * 给用户发送消息
     * @param $data 表单数据
     * @return $data
     * @date 2018/7/5 xuweiqi
     */
    function sendMessageData($data){
        if ($data['type'] == 0) {   //发送系统用户信息
            //查找用户名
            $list = model('UserDetail')->field('uid')->select();
            foreach ($list as $v) {
                $res=array(
                    'to_uid'=>$v['uid'],
                    'title'=>$data['title'],
                    'content' =>$data['content'],
                    'create_time' =>time(),
                    'type' =>$data['type'],
                    'status' =>1,
                );
                $rs = $this->insert($res);
            }
        } else if($data['type'] == 1) { //发送用户信息
            $map['name'] = $data['username'];
            if (empty($map['name'])) {
                return '请输入要接收消息的用户名！';
            }
            $uid = model('UserDetail')->where($map)->field('uid')->find();
            if (empty($uid['uid'])) {
//               $this->error = '对不起没有找到该用户！';
                return '对不起没有找到该用户！';
            }
            $res=array(
                'to_uid'=>$uid['uid'],
                'title'=>$data['title'],
                'content' =>$data['content'],
                'create_time' =>time(),
                'type' =>$data['type'],
                'status' =>1,
            );
            $rs = $this->insert($res);
        }else{
            unset($data['username']);
            $data['create_time']=time();
            $rs = $this->insert($data);
        }
        if($rs){
            return '发送消息成功！';
        }else{
            return '发送消息失败！';
        }
    }

    /***
     * 根据条件删除Message数据
     * @param $map 删除条件 'id'=>$id,
     * @return  bool
     * @date 2018/7/5 xuweiqi
     */
    function deleteMessageData($map){
        $data=$this->where($map)->delete();
        return $data;
    }

}
