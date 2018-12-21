<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\Message;
use app\common\model\UserDetail;
use think\Loader;
use think\Db;
use think\Request;
use think\Controller;

/**
 * 通知管理消息管理控制器
 */
class Msg extends Adminbase {

    //model
    private $Message;
    private $UserDetail;
    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Message =new Message();
        $this->UserDetail =new UserDetail();
    }

    public $type = array(0 => '系统通知', 1 => '用户通知', 2 => 'app通知');

    //发送消息列表
    public function index() {
        $data = Request::instance()->param();
        $map = array();
        //         $map= TeaTeacher::paginate(10)->toArray();
        if (isset($data['type']) ) {
            $map['m.type'] = array('like', '%' . input('type') . '%');
        }

        //两表联查Message user_detail
        $list=$this->Message->selectIndexMessageData($map);
        return view('',['all'=>$list,'type'=>$this->type]);
    }

    //发送消息
    public  function add(){
        return $this->fetch();
    }
//$str="Line1\nLine2\rLine3\r\nLine4\n";
//$order=array("\r\n","\n","\r");
//$replace='<br/>';
//
//$newstr=str_replace($order,$replace,$str);
    //发送消息执行动作
    public function add_run() {
        if (Request::instance()->isPost()) {
            $data=input('post.');
            //过滤textarea的换行符
            $data['content']=str_replace(array("\r\n","\n","\r"),'',$data['content']);
            //给全部用户和用户发送消息
            $rs=$this->Message->sendMessageData($data);
            $this->success($rs,'index');
        } else {
            return $this->error("未知请求或没有权限","index");
        }
    }
    
    //删除发送用户消息
    public function del($id){
        $map=  array('id'=>$id);
        //根据条件删除数据
        $res=$this->Message->deleteMessageData($map);
        if($res){
            $this->success("删除成功！","index");
        }else{
            $this->error("删除失败","index");
        }
    }


    //批量删除
    public function batchDelete($ids){
        $result = $this->Message->destroy($ids);
        return json(["success"=>"删除成功","ids"=>$result]);

    }

}
