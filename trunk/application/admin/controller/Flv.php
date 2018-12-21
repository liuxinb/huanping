<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\FlvMovie;
use app\common\model\FlvMovieUrl;
use app\common\model\TeaTeacher;
use app\common\model\FlvCategory;
use app\common\model\FlvPicture;
use think\Db;
use think\Request;
use think\Controller;
use think\Session;
use think\validate;

/**
 * 视频管理控制器
 */
class Flv extends Adminbase {

    //model
    private $FlvMovie;
    private $FlvCategory;
    private $TeaTeacher;
    private $FlvPicture;
    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->FlvMovie =new FlvMovie();
        $this->FlvCategory =new FlvCategory();
        $this->TeaTeacher =new TeaTeacher();
        $this->FlvPicture =new FlvPicture();
    }


    // 视频列表
    public function index()
    {
        $data = Request::instance()->param();
            $map = array();
            //         $map= TeaTeacher::paginate(10)->toArray();
            if (isset($data['keyword']) || isset($data['category'])) {
                $map['t.name'] = array('like', '%' . input('keyword') . '%');
                $map['c.title'] = array('like', '%' . input('category') . '%');
            }
            //  根据条件查询数据
            $datas = $this->FlvMovie->selectFlvMovieData($map);
            //查询条件为空时
            if(empty($datas)){
                return view('',['all'=>$datas,'page'=>$datas]);
            }
            //查询条件不为空组合数据
            $data=$this->FlvMovie->groupFlvMoviePub($datas);
            //渲染
            return view('', ['all' => $data,'page'=>$datas]);
    }

    // 视频删除
    public function flv_delete($id) {
        $map = array('id' => $id);
        $request = $this->FlvMovie->deleteFlvMovieField($map);
        if ($request) {
            $this->success("视频删除成功", "index");
        } else {
            $this->error("视频删除失败",'index');
        }

    }


    //添加视频
    public function add_flv() {
        $cateData = $this->FlvCategory->FlvCategoryGetTreeData();
        $teacherData = $this->TeaTeacher->showTeaCherData();
        $assign = array(
            'category' => $cateData,
            'teacherData' => $teacherData,
        );
        return view('',$assign);
    }

    protected $msg = [
        'hour' => '视频时长必须全为数字',
    ];

    //添加视频执行
    public function add_flv_run(Request $request) {
        if (Request::instance()->isPost()) {
            $validate = new Validate([
                'hour' => 'number',
////////                'hour' =>['/^1[34578]\d{9}$/'],  ['/^(\d+):(\d+):(\d+)$/'], [0-2]+:[0-6][0-9]:[0-6][0-9]
                    ], $this->msg);
            $Movie = $request->param();
            $datess['hour'] = $Movie['hour'];
            if (!$validate->check($datess)) {
                return $this->error($validate->getError(), 'index');
            }
            $hour['hour'] = $Movie['hour'] - 5;
            if ($hour['hour'] < 0) {
                return $this->error("视频时长不能小于5秒", 'index');
            }
//            $data = $Movie['hour'];
            $Movie['path'] = $this->upload($request);

//            $hour=$this->getSeconds($Movie['hour']); //存入时间秒
            $tran_result = true;
            // 启动事务
            Db::startTrans();
            try {
                if ( $movieDate =($this->FlvMovie->createFlvMovieDataPub($Movie,$hour))){
                    $req=$this->FlvMovie->createFlvMovieData($movieDate);
                    if ($req === false) {
                        // 提交事务
                        // Db::commit();
                        // $this->success("添加视频成功","index");
                        throw new Exception("错误原因");
                    }
                }
            } catch (\Exception $e) {
                $tran_result = false;
                // 回滚事务
                // Db::rollback();
            }

            if ($tran_result === false) {
                Db::rollback();
                $this->error("添加视频错误", "add_flv");
            } else {
                Db::commit();
                // 更新成功
                $this->success("添加视频成功", "index");
            }
        } else {
            $this->error("未知请求或没有权限");
        }
    }

//    获取时长 转成秒 时分秒 00:00:00  因有误差 所有视频存入时长减五秒
    function getSeconds($string) {
        $arr = explode(':', $string);
        $len = count($arr);
        return (int) $arr[$len - 1] + (int) $arr[$len - 2] * 60 + (($len > 2 ? (int) $arr[$len - 3] : 0) * 60 * 60) - 5;
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
            $uploadDir = '/uploads/web.flv/';
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
        $cateData = $this->FlvCategory->FlvCategoryGetTreeData();
        $teacherData =$this->TeaTeacher->showTeaCherData();
        $info = $this->FlvMovie->findFlvMovieField($id);
        $assign = array(
            'category' => $cateData,
            'teacherData' => $teacherData,
            'info' => $info
        );
        return view('',$assign);
    }

    /**
     *      把秒数转换为时分秒的格式 
     *      @param Int $times 时间，单位 秒 
     *      @return String 
     */
    function secToTime($times) {
        $result = '00:00:00';
        if ($times > 0) {
            $hour = floor($times / 3600);
            $minute = floor(($times - 3600 * $hour) / 60);
            $second = floor((($times - 3600 * $hour) - 60 * $minute) % 60);
            $result = $hour . ':' . $minute . ':' . $second;
        }
        return $result;
    }

    //修改视频执行动作
    public function edit_run(Request $request, $id) {
        if (Request::instance()->isPost()) {
            $Movie = input('post.');
            $validate = new Validate([
                'hour' => 'number',
                    ], $this->msg);
            $datess['hour'] = $Movie['hour'];
            if (!$validate->check($datess)) {
                return $this->error($validate->getError(), 'index');
            }
            $hour['hour'] = $Movie['hour'] - 5;
            if ($hour['hour'] < 0) {
                return $this->error("视频时长不能小于5秒", 'index');
            }
            if (!empty($res = $this->upload($request))) {
                $Movie['path'] = $res;
            } else {
                unset($Movie['path']);
            }
            $map=array('id' => $id);
//            $resData = $this->FlvMovie->valueFlvMovieFieldCoverId($map);
//            $pic['create_time'] = time();
//            $hour=$this->getSeconds($Movie['hour']);   //获取时长00:00:00的秒数
            $tran_result = true;
            // 启动事务
            Db::startTrans();
            try {
                //修改视频FlvPicture FlvMovie FlvMovieUrl
                $res=$this->FlvPicture->updateFlvPicturePubData($Movie,$map,$hour);
                if($res==false){
                    throw new Exception("修改错误");
                }
            } catch (\Exception $e) {
                $tran_result = false;
                // 回滚事务
                // Db::rollback();
            }

            if ($tran_result === false) {
                Db::rollback();
                $this->error("您没有任何修改！", "index");
            } else {
                Db::commit();
                // 更新成功
                $this->success("修改视频成功", "index");
            }
        } else {
            $this->error("未知请求或没有权限");
        }
    }

    //视频价格管理 pid=0 为第一类
    public function bag_price() {
        $data = FlvCategory::where('pid = 0')
                        ->order('update_time desc')->paginate(10);
        $this->assign('all', $data);
        return $this->fetch();
    }

    //视频播放
    public function boli($id){
        $map=array('id'=>$id);
        //获取视频vid
        $data=$this->FlvMovie->getFlvMovieVidField($map);
        return view('',['all'=>$data]);
    }

    //修改推荐状态 推荐和不推荐
    public function editPosition(Request $request)
    {
        $data = $request->param();
        $dataMap= array( 'id' => $data['id']);
        $dataDa=array('position' => $data['position']) ;
        $result = $this->FlvMovie->dataUpdate($dataMap,$dataDa);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}
