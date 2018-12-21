<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\PromoteVideo;
use app\common\model\TeaTeacher;
use Prophecy\Argument\Token\IdenticalValueToken;
use think\Db;
use think\Request;
use think\Controller;
use think\Session;
use think\validate;

/**
 * 视频管理控制器
 */
class Promote extends Adminbase {

    //model
    private $PromoteVideo;
    private $TeaTeacher;
    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->PromoteVideo =new PromoteVideo();
        $this->TeaTeacher =new TeaTeacher();
    }


    // 视频列表
    public function index()
    {
        $data = Request::instance()->param();
        $map = array();
        if (isset($data['keyword']) || isset($data['title'])) {
            $map['t.name'] = array('like', '%' . input('keyword') . '%');
            $map['p.title'] = array('like', '%' . input('title') . '%');
        }
        //  根据条件查询数据
        $datas = $this->PromoteVideo->selectPromoteData($map);
//        //查询条件为空时
//        if(empty($datas)){
//            return view('',['all'=>$datas,'page'=>$datas]);
//        }
//        //查询条件不为空组合数据
//        $data=$this->PromoteVideo->groupFlvMoviePub($datas);
        //渲染
        return view('', ['all' => $datas]);
    }

    // 视频删除
    public function flv_delete($id) {
        $map = array('id' => $id);
        $request = $this->PromoteVideo->delPromoteData($map);
        if ($request) {
            $this->success("视频删除成功", "index");
        } else {
            $this->error("视频删除失败",'index');
        }

    }


    //添加视频
    public function add_flv() {
        $teacherData = $this->TeaTeacher->showTeaCherData();
        $assign = array(
            'teacherData' => $teacherData,
        );
        return view('',$assign);
    }

    //添加视频执行
    public function add_flv_run(Request $request) {
        if (Request::instance()->isPost()) {
            $Movie = input("post.");
            $Movie['cover'] = $this->upload($request);
            $Movie['create_time']=time();
            $Movie['state']=0;
            $addRes=$this->PromoteVideo->addPromoteData($Movie);
            if($addRes){
                $this->success("添加成功","index");
            }else{
                $this->error("添加失败","index");
            }

        } else {
            $this->error("未知请求或没有权限");
        }
    }

    //文件上传
    public function upload(Request $request) {
        //上传图片类
        $file = $request->file('image');
        if (true !== $this->validate(['image' => $file], ['image' => 'image'])) {
            $this->error('请选择正确的图像文件', 'index');
        }
        if (!empty($file)) {
            // 移动到框架应用根目录/public/uploads/ 目录下
            $uploadDir =  '/uploads/web/flv/';
            //        $info = $file->move(ROOT_PATH . 'public' . $uploadDir);
            $info = $file->move(ROOT_PATH . 'public' . $uploadDir);
            if ($info) {
                //将上传文件的路径返回给客户端
                return $uploadDir . $info->getSaveName();
            } else {
                return '';
            }
        }
    }

    //修改视频
    public function edit($id) {
        $teacherData =$this->TeaTeacher->showTeaCherData();
        $info = $this->PromoteVideo->findPromoteField($id);
        $assign = array(
            'teacherData' => $teacherData,
            'info' => $info
        );
        return view('',$assign);
    }



    //修改视频执行动作
    public function edit_run(Request $request, $id) {
        if (Request::instance()->isPost()) {
            $Movie = input('post.');
            if (!empty($res = $this->upload($request))) {
                $Movie['path'] = $res;
            } else {
                unset($Movie['path']);
            }
            $map=array('id' => $id);
            //修改视频FlvPicture FlvMovie FlvMovieUrl
            $res=$this->PromoteVideo->updatePromotePubData($map,$Movie);
            if($res){
               $this->success("修改成功","index");
            }else{
                $this->error("修改失败","index");
            }

        } else {
            $this->error("未知请求或没有权限");
        }
    }


    //视频播放
    public function boli($id){
        $map=array('id'=>$id);
        //获取视频vid
        $data=$this->PromoteVideo->getPromoteField($map);
        return view('',['all'=>$data]);
    }

    //修改推荐状态 推荐和不推荐
    public function editPosition(Request $request)
    {
        $data = $request->param();
        $dataMap= array( 'id' => $data['id']);
        $dataDa=array('state' => $data['state']) ;
        $result = $this->PromoteVideo->updatePromotePubData($dataMap,$dataDa);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}
