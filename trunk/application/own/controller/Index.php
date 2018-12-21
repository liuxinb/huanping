<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/3
 * Time: 上午11:30
 */

namespace app\own\controller;

use app\common\model\Draw;
use app\common\model\EnterpriseOrder as OrdersModel;
use app\common\model\Record as RecordModel;
use app\common\model\CollegeRecord;
use app\common\model\FlvCategory;
use app\common\model\User;
use think\Controller;
use think\Request;
use think\Session;

/**
 * Class Index
 * @package app\own\controller
 */
class Index extends Controller
{
    /**
     * @var FlvCategory
     */
    private $flvcategory;
    /**
     * @var User
     */
    private $user;
    /**
     * @var CollegeRecord
     */
    private $collegeRecord;

    /**
     * Index constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        if (!Session::has('user.phone')) $this->redirect('/');
        $this->flvcategory = new FlvCategory();
        $this->user = new User();
        $this->collegeRecord = new CollegeRecord();
    }


    /**
     * 个人首页
     * @return \think\response\View
     * @user 李海江 2018/9/3~上午11:31
     */
    public function index()
    {
        //获取手机号
        $phone = Session::get('user.phone');
        //获取用户个人信息
        $message = model('user')->BaseWithFind('adminUserDetail', ['phone' => $phone]);
        $webMessage = array(
            'name' => !empty($message->adminUserDetail->name) ? $message->adminUserDetail->name : '未设置',
            'avatar' => !empty($message['adminUserDetail']['avatar']) ? $message['adminUserDetail']['avatar'] : config('defaultavatar'),
        );
        //渲染
        $this->assign('message', $webMessage);
        return view();
    }

    /**
     * 个人信息页
     * @return \think\response\View
     * @user 李海江 2018/9/6~下午1:55
     */
    public function ownmessage()
    {
        $phone = Session::get('user.phone');
        $message = model('user')->BaseWithFind('adminUserDetail', ['phone' => $phone]);
        $webData = array(
            'name' => $message->adminUserDetail->name,
            'phone' => $message->phone,
            'idnumber' => $message->adminUserDetail->idnumber,

        );

        //获取自己的信息
        if ($message->type === 0) {
            $webData['school_name'] = '您是企业用户';
            $webData['subject_name'] = '您是企业用户';
        } else {
            //获取全部院校信息
            $allSchoolData = model('collegeRecord')->BaseSelect(['pid' => 0]);
            foreach ($allSchoolData as $k => $v) {
                $allSchool[$v->admin_id] = $v->academy_name;
            }
            //如果个人的系id存在,查询名字
            if (!empty($message->subjects_id)) {
                $subject_name = model('collegeRecord')->BaseFind(['id' => $message->subjects_id])->academy_name;
            }
            $webData['school_name'] = $allSchool[$message->enterprise_id];
            $webData['subject_name'] = $subject_name;
            $webData['allSchool'] = $allSchoolData;
        }

        $this->assign('data', $webData);
        return view();
    }

    /**
     * 获取院校下的所有系名
     * @param Request $request
     * @return mixed
     * @user 李海江 2018/9/6~下午1:55
     */
    public function getSubject(Request $request)
    {
        $school_id = $request->post('school_id');
        $list = model('collegeRecord')->BaseSelect(['pid' => $school_id], ['id', 'academy_name']);
        return $list;
    }

    /**
     * 课程列表
     * @return \think\response\View
     * @user 李海江 2018/9/7~下午2:17
     */
    public function classList()
    {
        //获取手机号
        $phone = Session::get('user.phone');
        //查询自己信息的状态
        $res = $this->user->getOwnMessageState($phone);
        if ($res) {
            return ['error' => true];
        }

        //判断自己是属于哪个支付状态的价格(查看自己的院校是否签约)
        $res = $this->user->BaseFind(['phone' => $phone]);
        if($res->type == 1 && $res->enterprise_id == 33)//昆明学院学生课程写死
        {
            $param ['pid'] = 0;
            $param['id'] = 9;
        }else{
            $param ['pid'] = 0;
            $param['status'] = 1;
        }
        //获取基础课程信息
        $list = $this->flvcategory->BaseSelect($param, ['id', 'title', 'bag_img', 'sign_price', 'own_bag_price']);

        //如果我是院校用户
        if ($res['type'] == 1) {
            //查询我的院校签没签约
            $count = $this->collegeRecord->BaseSelectCount(['state' => 1, 'admin_id' => $res->enterprise_id]);
        }
        //如果是签约企业下的学员
        foreach ($list as $k => $v) {
            if ($count != 0) {
                $v['price'] = $v['sign_price'];
            } else {
                $v['price'] = $v['own_bag_price'];
            }
        }
        //渲染页面
        $this->assign('list', $list);
        return view();
    }

    /**
     * 课程详情
     * @return \think\response\View
     * @user 李海江 2018/9/7~上午9:20
     */
    public function classShow(Request $request)
    {
        //获取手机号
        $phone = Session::get('user.phone');
        //查询自己信息的状态
        $res = $this->user->getOwnMessageState($phone);
        if ($res) {
            return ['error' => true];
        }
        //获取课程包id
        $id = $request->param('id');
        //获取价格
        $price = $request->param('price');
        $res = $this->flvcategory->BaseFind(['id' => $id]);
        $res['price'] = $price;
        //获取该课程包下的课程
        $data = $this->flvcategory->BaseWithSelect('teacher', ['pid' => $id], ['title', 'hours']);
        $orderModel = new OrdersModel();
        $uid = model("User")->getUidByphone(\session('user.phone'));
        $order = $orderModel->BaseFind(['type' => 3, 'enterprise_id' => $uid, 'category_id' => $id]);
        $this->assign("buyed", 0);
        if ($order) {
            $this->assign("buyed", $order["order_status"]);
        }
        //渲染页面
        $this->assign('res', $res);
        $this->assign('data', $data);
        return view();
    }

    /**
     * 订单管理
     * @return \think\response\View
     * @user 李海江 2018/9/7~上午9:20
     */
    public function order()
    {
        //获取手机号
        $phone = Session::get('user.phone');
        //查询自己信息的状态
        $res = $this->user->getOwnMessageState($phone);
        if ($res) {
            return ['error' => true];
        }
        $type = 3;
        $uid = model("User")->getUidByphone(\session('user.phone'));
        $OrdersModel = new OrdersModel;
        $RecordModel = new RecordModel;
        //获取已支付订单
        $payMap = array('e.enterprise_id' => $uid, 'type' => $type, 'order_status' => 1);
        $payList = $OrdersModel->getOrderJoinByFirmId($payMap);
        $unpayMap = array('e.enterprise_id' => $uid, 'type' => $type, 'order_status' => 2);
        //未支付订单
        $unpayList = $OrdersModel->getOrderListByFirmId($unpayMap);
//        dd($payList);

        if ($type == 1 || $type == 2) {
            $map['enterId'] = $uid;
        }
        //获取详细信息
        $arrRecord = $RecordModel->BaseFind($map);

        $this->assign('arrRecord', $arrRecord);
        $this->assign('payList', $payList);
        $this->assign('unpayList', $unpayList);
        return view('index/order');

    }

    /**
     * 保存用户信息
     * @return array
     * @user 李海江 2018/9/7~上午9:20
     */
    public function saveOwnMessage()
    {
        //获取指定字段
        $webData = Request::instance()->only('name,enterprise_id,subjects_id,idnumber');
        //判断用户名是否为空
        if (empty($webData['name'])) return ['code' => -1, 'message' => '请填写姓名'];
        if (!preg_match('/^[\x{4e00}-\x{9fa5}]{2,9}$/u', $webData['name'])) return ['code' => -1, 'message' => '姓名格式不正确'];

        if (!validation_filter_id_card($webData['idnumber'])) return ['code' => -1, 'message' => '身份证格式不正确'];
        if (!empty($webData['enterprise_id'])) {
            //判断院校是否存在
            $school = model('CollegeRecord')->BaseFind(['id' => $webData['enterprise_id']]);
            if (empty($school)) {
                return ['code' => -1, 'message' => '院校选择错误,请重试'];
            } else {
                $webData['enterprise_id'] = $school->admin_id;
            }
        }
        if (!empty($webData['subject_id'])) {
            //判断系是否存在,并且所选系是否是所选院校下的
            $subjectCount = model('CollegeRecord')->BaseSelectCount(['id' => $webData['subjects_id'], 'pid' => $webData['enterprise_id']]);
            if ($subjectCount == 0) return ['code' => 0, 'message' => '专业选择错误,请重试'];
        }
        //符条件保存user表里的数据
        $res = $this->user->BaseUpdate($webData, ['phone' => Session::get('user.phone')]);
        //获取uid
        $uid = model('User')->BaseFind(['phone' => Session::get('user.phone')], ['id'])->id;
        $res1 = model('UserDetail')->BaseUpdate($webData, ['uid' => $uid]);
        if ($res || $res1) {
            return ['code' => 1, 'message' => '编辑成功'];
        } else {
            return ['code' => 0, 'message' => '编辑失败,请稍后重试'];
        }
    }

    /**
     * 退出
     * @user 李海江 2018/9/8~上午10:29
     */
    public function doexit()
    {
        Session::clear();
        return true;
    }


    /**
     * 发票抬头
     * @return \think\response\View
     * @user 李海江 2018/9/12~下午9:37
     */
    public function ticket()
    {
        //获取手机号
        $phone = Session::get('user.phone');
        //查询自己信息的状态
        $res = $this->user->getOwnMessageState($phone);
        if ($res) {
            return ['error' => true];
        }

        $uid = model("User")->getUidByphone(\session('user.phone'));
        $dramModel = new Draw();
        $dataDraw = $dramModel->BaseFind(['uid' => $uid, 'type' => 3]);
        if (!$dataDraw) {
            $dataDraw['id'] = 0;
            $dataDraw['invoice_name'] = "";
            $dataDraw['identification'] = "";
            $dataDraw['phone'] = "";
            $dataDraw['address'] = "";
            $dataDraw['bank'] = "";
            $dataDraw['number'] = "";
        }
        return view('ticket', ['dataDraw' => $dataDraw]);
    }

    //院校发票修改
    public function saveTicket(Request $request)
    {
        $uid = model("User")->getUidByphone(\session('user.phone'));
        $type = 3;
        $dataArray = $request->param();
        //验证发票名称
        if (!preg_match('/^[\(\)\x{4e00}-\x{9fa5}A-Z_]+$/u', $dataArray['invoice_name'])) {
            return "发票名称不符合规则，只支持中文，大写英文，英文小括弧和下划线";
        }
        //验证发票名称
        if (!preg_match('/^((\d{6}[0-9A-Z]{9})|([0-9A-Za-z]{2}\d{6}[0-9A-Za-z]{10})|([0-9A-Za-z]{20}))$/', $dataArray['identification'])) {
            return "纳税人识别号不正确";
        }
        $DrawCommonmodel = new Draw();
        if ($dataArray['id'] > 0) {
            $dataDraw = $DrawCommonmodel->BaseFind(["uid" => $uid, "type" => 3])->toArray();
            if (!$dataDraw) {
                return "1111";
            }
        }
        $dataDraw['type'] = $type;
        $dataDraw['uid'] = $uid;
        $dataDraw['invoice_name'] = $dataArray['invoice_name'];
        $dataDraw['identification'] = $dataArray['identification'];
        $dataDraw['phone'] = $dataArray['phone'];
        $dataDraw['address'] = $dataArray['address'];
        $dataDraw['bank'] = $dataArray['bank'];
        $dataDraw['number'] = $dataArray['number'];
        if ($dataArray['id'] == 0) {
            $result = $DrawCommonmodel->BaseSave($dataDraw);
        } else {
            $result = $DrawCommonmodel->BaseUpdate($dataDraw, ['id' => $dataArray['id'], 'uid' => $uid, 'type' => 3]);
        }
        if ($result) {
            return '操作成功';
        } else {
            return '尚未做修改操作';
        }

    }
}