<?php

namespace app\admin\Controller;

use app\common\controller\Adminbase;
use app\common\model\PlugList;
use think\Request;

class Plug extends Adminbase {

    //model
    private $PlugList;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->PlugList = new PlugList();

    }
    /**
     * 广告列表
     */
    public function index() {
        //查询Pluglist表数据
        $data = $this->PlugList->paginatePlugListData();
        return view('',['p_list'=>$data]);
    }

    //添加广告操作
    public function add_adv(Request $request) {
        if ($this->request->isPost()) {
            $data = input("post.");
            $data['plug_pic'] = $this->upload($request);
            $data['plug_addtime'] = date('Y-m-d H:i:s');
            $request =  $this->PlugList->insertPlugListData($data);
            if ($request) {
                return $this->success('广告添加成功', "Admin/Plug/index", 1);
            } else {
                return $this->error('广告添加失败');
            }
        }
        return view('');
    }

    //文件上传
    public function upload(Request $request) {
        $file = $request->file('image');
        if (true !== $this->validate(['image' => $file], ['image' => 'image'])) {
            $this->error('请选择正确的图像文件', 'index');
        }
        if (!empty($file)) {
            // 移动到框架应用根目录/public/uploads/ 目录下
            $uploadDir = '/uploads/web/adv/';
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

    /*
     * 修改广告信息
     */

    public function update_adv($id) {
        $map = array('id' => $id,);
        //根据条件查找信息
        $data =  $this->PlugList ->findPlugListData($map);
        return view('',['ad_find'=>$data]);
    }

    /*
     * 修改广告信息
     */

    public function update_adv_run(Request $request, $id) {
        if ($this->request->isPost()) {
            $data = input("post.");
            if (!empty($res = $this->upload($request))) {
                $data['plug_pic'] = $res;
            } else {
                unset($data['plug_pic']);
            }
            $data['plug_addtime'] = date('Y-m-d H:i:s');
            $map=array('id' => $id);
            //根据条件更新数据
            $request=  $this->PlugList ->updatePluglistData($map,$data);
            if ($request) {
                return $this->success('广告修改成功', "Admin/Plug/index", 1);
            } else {
                return $this->error('广告修改失败');
            }
        }
    }

    //删除广告信息
    public function Plug_list_delete($id) {
        $map = array( 'id' => $id,);
        $request = $this->PlugList->deletePluglistData($map);
        if ($request) {
            return $this->success('广告删除成功', 'Admin/Plug/index');
        } else {
            return $this->error('广告删除失败', 'Admin/Plug/index');
        }
    }


    //修改推荐状态 推荐和不推荐
    public function editState(Request $request)
    {
        $data = $request->param();
        $dataMap= array( 'id' => $data['id']);
        $dataDa=array('plug_status' => $data['state']) ;
        $result = $this->PlugList->updatePluglistData($dataMap,$dataDa);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }


}
