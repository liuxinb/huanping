<?php

namespace app\enterprise\controller;
use app\common\controller\EnterBase;
use app\common\model\User;
use app\common\model\FlvCategory;
use app\common\model\UserPlan;
use app\common\model\Train as TrainModel;
use app\common\model\EnterpriseOrder;
use app\common\model\Exam;
use app\common\model\Videoplayer;
use think\Request;
use \think\Validate;

class Train extends EnterBase
{
    private $userModel;
    private $flvcategoryModel;
    private $userplanModel;
    private $usertrainModel;
    private $adminRole;

    protected $msg = [
        'train_name.require' => '培训名称不能为空',
        'create_time.require' => '开始时间不能为空',
        'create_time' => '入职时期格式错误',
        'update_time.require' => '结束不能为空',
        'cid.require' => '课程不能为空',
    ];

    protected $validate = [
        'name' => 'require',
        'create_time' => ['/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', 'require'],
        'update_time' => ['/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', 'require'],
        'cid' => 'require',
    ];

    public function __construct()
    {
        $this->userModel = new User();
        $this->flvcategoryModel = new FlvCategory();
        $this->userplanModel = new UserPlan();
        $this->usertrainModel = new TrainModel();
        $this->enterpriseorderModel = new EnterpriseOrder();
        $this->ExamModel = new Exam();
        $this->VideoplayerModel = new Videoplayer();
        $this->adminRole = parent::role();
    }

    public function index()
    {
        $enterpriseId = session('rootId');
        $traindata = $this->usertrainModel->selectTrainData($enterpriseId);
        $data = $this->VideoplayerModel->getVideoplan($enterpriseId,$traindata);
        $pageResult = $data->render();
        return view('train/index',
            [
                'trainData' => $data,
                'page' => $pageResult,
                'adminRole' => $this->adminRole
            ]);
    }

    public function add()
    {
        //获取ID
        $enterpriseId = session('rootId');
        //检查企业订单状态
        $paystatus = $this->enterpriseorderModel->getOrderpaystatus($enterpriseId);

        //提示没订单 院校不提示
        if ($this->adminRole == 1) {
            if (!$paystatus) {
                return $this->success('您还未购买任何课程，不可以添加培训计划!','/train');
            }
        }
        //获取已购课程包
        if ($this->adminRole == 1) {
            $flvcategoryData = $this->flvcategoryModel->getSelectCategory($paystatus);
        } else {
            $flvcategoryData = $this->flvcategoryModel->getAcademySelectCategory();
        }
        //返回
        return view('train/add',
            [
                'flvcategoryData'=> $flvcategoryData,
                'adminRole' => $this->adminRole
            ]);
    }

    public function save(Request $request)
    {
        //获取ID
        $enterpriseId = session('rootId');
        //处理表单数据
        $data = $request->param();
        //验证数据合法性
        $validate = new Validate($this->validate, $this->msg);
        if (!$validate->check($data)) {
            echo $validate->getError();die;
        }
        if ($this->adminRole == 1) {
            if (empty($data['uid'])) {
                echo '培训学员不能为空';die;
            }
        }
        //验证时间顺序
        $startTime = strtotime($data['create_time']);
        $endTime = strtotime($data['update_time']);
        if ($startTime >= $endTime) {
            echo '开始时间不能大于等于结束时间';die;
        }
        //更新数据
        $result = $this->userplanModel->userplanCreateData($data);
        //返回
        if ($result) {
            return '添加成功';
        } else {
            return '添加失败';
        }
    }

    public function studyList(Request $request)
    {
        $cid = $request->param('id');
        //课程包cid查询集中培训学员
        $userId = $this->userplanModel->getUserId($cid);
        if (empty($userId)) {
            return view('train/studylist',['data' => [], 'list' => [],'cid' => []]);
        }
        //企业id
        $enterpriseId = session('rootId');
        //请求
        $requestData = $request->except(['page']);
        //获取数据
        $dataResult = $this->userModel->getStudyData($requestData, $enterpriseId,$userId);
        //证书
        $studyResult = $this->ExamModel->getcertificate($dataResult);
        //获取进度
        $studyplanResult = $this->userModel->getStudyPlan($studyResult);
        //分页
        $pageResult = $studyResult->render();
        //返回
        return view('train/studylist',['data' => $studyplanResult, 'list' => $pageResult,'cid' => $cid]);
    }

    public function videoList(Request $request)
    {
        $cid = $request->param('cid');
        $trainId = $request->param('id');
        $videoData = $this->flvcategoryModel->getListCategory($cid,$trainId);
        return view('train/videolist',['videoData'=> $videoData]);
    }

    public function userList(Request $request)
    {
        //获取ID
        $enterpriseId = session('rootId');
        //请求
        $userRequest = $request->except(['page', 'result']);
        //获取数据
        $searchResult = $this->userModel->getUsersData($userRequest, $enterpriseId);
        //分页
        $pageResult = $searchResult->render();

        $userIds = $request->param("userIds");
        $arr = explode(',',$userIds);

        //总数
        $total = $this->userModel->getCount($enterpriseId);
        //返回
        return view('train/userlist',['data' => $searchResult, 'page' => $pageResult, 'total' => $total,'userIds'=>$arr]);
    }

    public function saveVideoplan(Request $request)
    {
        //获取ID
        $enterpriseId = session('rootId');
        //ajax请求
        $resultData = $request->param();
        //处理数据
        $data = [
            'enterprise_id' => $enterpriseId,
            'movie_paln' => $resultData['videoTime'],
            'movie_url' => $resultData['url'],
            'train_id' => $resultData['id']
        ];
        //查询企业播放记录，没有记录新增记录，有记录修改记录
        $this->VideoplayerModel->addTrainvideoplan($data);
    }
}