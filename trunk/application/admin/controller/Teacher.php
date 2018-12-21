<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\TeaTeacher;
use think\Request;

/**
 * 教师管理控制器  
 */
class Teacher extends AdminBase {

    //model
    private $TeaTeacher;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->TeaTeacher =new TeaTeacher();
    }

    //教师列表
    public function index() {
        $data = Request::instance()->param();
        $map = array();
        if (isset($data['keyword'])) {
            $map['name'] = array('like', '%' . input('keyword') . '%');
        }
        $data = $this->TeaTeacher->selectTeaCherData($map);
        return view('',['all'=>$data]);
    }


//    添加教师
    public function add(Request $request) {
        if (Request::instance()->isPost()) {
            $data =$this->TeaTeacher->teaPub();
            if (true !== $this->validate(['phone' => $data['phone']], ['phone' => 'require|/^1[34578]\d{9}$/'])) {
                 $this->error('手机号格式错误', 'index');
            }
            $data['path'] = $this->upload($request);
            $res = $this->TeaTeacher->insertTeaCherData($data);
            if ($res) {
                $this->success("添加教师成功", 'index');
            } else {
                $this->error("添加教师失败", 'index');
            }
        } else {
            $this->error("未知请求或没有权限", "index");
        }
    }

    //文件上传
    public function upload(Request $request) {
        //上传图片类
        $file = $request->file('image');
//	        if(empty($file)){
//	            $this->error('请选择上传文件');
//	        }
        //校验器，判断图片格式是否正确
        if (true !== $this->validate(['image' => $file], ['image' => 'image'])) {
            $this->error('请选择正确的图像文件', 'index');
        }
        if (!empty($file)) {
            // 移动到框架应用根目录/public/uploads/ 目录下
            $uploadDir = '/uploads/web/teacher/';
            //$info = $file->move(ROOT_PATH . 'public' . $uploadDir);
            $info = $file->move(ROOT_PATH . 'public' . $uploadDir);
            if ($info) {
                //将上传文件的路径返回给客户端
                return $uploadDir . $info->getSaveName();
            } else {
                return $file->getError();
            }
        }
    }

    //修改教师
    public function edit(Request $request) {
        if (Request::instance()->isPost()) {
            $data = $this->TeaTeacher->teaPub();
             if (true !== $this->validate(['phone' => $data['phone']], ['phone' => 'require|/^1[34578]\d{9}$/'])) {
                 $this->error('手机号格式错误', 'index');
            }
            if (!empty($res = $this->upload($request))) {
                $data['path'] = $res;
            } else {
                unset($data['path']);
            }
            $res = $this->TeaTeacher->updateTeaCherData($data);
            if ($res) {
                $this->success("修改教师成功", 'index');
            } else {
                $this->error("修改教师失败", 'index');
            }
        } else {
            $this->error("未知请求或没有权限", "index");
        }
    }


//    删除教师
    public function del($id) {
        $map=['id' => $id];
        $res = $this->TeaTeacher->deleteTeaCherData($map);
        if ($res) {
            $this->success("删除教师成功", 'index');
        } else {
            $this->error("删除教师失败", 'index');
        }
    }



}
