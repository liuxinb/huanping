<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\Auth_group;
use app\common\model\FlvCategory;
use app\common\model\TeaTeacher;
use think\Db;
use think\Request;
use think\Validate;

/**
 * 
 * 后台资源分类管理
 */
class Category extends AdminBase {

    //model
    private $FlvCategory;
    private $TeaTeacher;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->FlvCategory =new FlvCategory();
        $this->TeaTeacher =new TeaTeacher();
    }
    //******************视频***********************
    protected $msg = [
        'bag_price.number' => '企业价格格式不正确',
        'own_bag_price.number' => '个人购买价格格式不正确',
        'sign_price.number' => '签约价格格式不正确',
    ];

    /* 分类列表 */

    public function index() {

        //获取flvcategory树型数据
        $data = $this->FlvCategory->FlvCategoryGetTreeData();
        //获取教师数据
        $teacherData = $this->TeaTeacher->showTeaCherData();
        $assign = array(
            'data' => $data,
            'teacherData' => $teacherData,
        );

        return view('',$assign);
    }


    /* 添加子类 */
    public function add(Request $request) {
        if ($this->request->isPost()) {
            $data = input('post.');
            $validate = new Validate([
                'bag_price' => 'number',
                'own_bag_price' => 'number',
                'sign_price' => 'number',
                    ], $this->msg);
            $price['bag_price'] = $data['bag_price'];
            $price['own_bag_price'] = $data['own_bag_price'];
            $price['sign_price'] = $data['sign_price'];
            if (!$validate->check($price)) {
                return $this->error($validate->getError(), 'index');
            }
            //根据pid查到父类对应的id，查到父类的状态，0：禁止 1：启用，可以添加子类
//            $map = array('id' => $data['pid']);
//            $status = FlvCategory::where($map)->field("status")->find();
//            if (!($status['status'] == NULL || $status['status'] !== 1)) {
//                return $this->error('指定的上级分类不存在或被禁用！');
//            }
            //调用上传方法 获取上传地址
            $data['bag_img'] = $this->upload($request);
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['update_time'] = date('Y-m-d H:i:s');

              // 获取数据插入数据
            $result =$this-> FlvCategory->insertFlvCategory($request,$data);
            if ($result) {
                $this->success('添加成功', 'index');
            } else {
                $this->error('添加失败,请选择正确的图片','index');
            }
        } else {
            return $this->error("数据请求错误",'index');
        }
    }

    //文件上传
    public function upload($request) {
        $file = $request->file('imageUpload');
        if (true!== $this->validate(['image' => $file], ['image' => 'image'])) {
            return $this->error('请选择正确的图片','index');
        }

        if (!empty($file)) {
            // 移动到框架应用根目录/public/uploads/ 目录下
            $uploadDir = '/ploads/web/category/';
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



    /* 修改子类 */

    public function edit(Request $request) {
        $data = input('post.');
        $validate = new Validate([
            'bag_price' => 'number',
            'own_bag_price' => 'number',
            'sign_price' => 'number',
                ], $this->msg);
        $price['bag_price'] = $data['bagEdit'];
        $price['own_bag_price'] = $data['own_bag_price'];
        $price['sign_price'] = $data['sign_price'];
        if (!$validate->check($price)) {
            return $this->error($validate->getError(), 'index');
        }
        $info = ['title' => $data['title'], 'name' => $data['name'], 'update_time' => date('Y-m-d H:i:s'), 'bag_price' => $data['bagEdit'], 'own_bag_price' => $data['own_bag_price'],'sign_price' => $data['sign_price'], 'teacher_id' => $data['teacher_id'], 'description' => $data['description']];
        if (!empty($res = $this->upload($request))) {
            $info['bag_img'] = $res;
        } else {
            unset($data['bag_img']);
        }
        $map=["id" => $data['id']];
        $result = $this->FlvCategory->updataFlvCategory($map,$info);
        // $result=\app\admin\model\Admin::change(["id"=>$data['id']],$info);
        if ($result) {
            $this->success('修改成功', 'index');
        } else {
            $this->error('您没有做任何修改','index');
        }
    }

    /**
     * 删除子类
     */
    public function delete($id) {
        $data = $this->FlvCategory->findFlvCategoryData($id);
        if ($data) {
            $this->error('请先删除子类', 'index');
        }
        $result =$this-> FlvCategory->delFlvCategoryData($id);
        if ($result) {
            $this->success('删除成功', 'index');
        } else {
            $this->error('请先删除子类','index');
        }
    }

    /*
     * 点击添加子类的禁止或开启操作
     */

    public function status($id) {
        $map = array('id' => $id);
        //根据状态 更新为相应的状态
        $updateStatus=$this->FlvCategory->findFlvCategoryStatus($map);
        if ($updateStatus) {
            return $this->success("状态操作成功", 'index');
        } else {
            return $this->error("状态操作失败",'index');
        }
    }

}
