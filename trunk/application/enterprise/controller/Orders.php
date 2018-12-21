<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/6/11
 * Time: 18:20
 */

namespace app\enterprise\controller;

use app\common\controller\EnterBase;
use app\common\model\EnterpriseOrder as OrdersModel;
use app\common\model\OrderLog as OrderslogModel;
use app\common\model\FlvCategory as FlvCategoryModel;
use app\common\model\OrderPay as OrderPayModel;
use app\common\model\Record as RecordModel;
use app\common\model\CollegeRecord as CollegeRecordModel;
use think\Db;
use think\Loader;
use think\Session;
use think\Cookie;

class Orders extends EnterBase
{
    public function __construct()
    {
        parent::__construct();
        $userId = \session('rootId');
        if (empty($userId)) {
            $rootCookie = Cookie::get('rootId');
            if (!empty($rootCookie)) {
                \session('rootId', $rootCookie);
            }
        }
    }

    public function zfbpay()
    {
        return $this->fetch();
    }

    public function index()
    {
        //企业id
        $user = \session('rootId');
        $type = \session('adminRole')->type;

        //走院校合并创建订单
        if ($type == 2) {
            return $this->selbatch();
        }
        $OrdersModel = new OrdersModel;
        $RecordModel = new RecordModel;
        //获取已支付订单
        $payMap = array('e.enterprise_id' => $user, 'type' => $type, 'order_status' => 1);
        $payList = $OrdersModel->getOrderJoinByFirmId($payMap);
//dd($user);
        $unpayMap = array('e.enterprise_id' => $user, 'type' => $type, 'order_status' => 2);

        //未支付订单
        $unpayList = $OrdersModel->getOrderListByFirmId($unpayMap);
        if ($type == 1 || $type == 2) {
            $map['enterId'] = $user;
        }
        //获取详细信息
        $arrRecord = $RecordModel->BaseFind($map);

//        dd($arrRecord);
        $this->assign('arrRecord', $arrRecord);
        $this->assign('payList', $payList);
        $this->assign('unpayList', $unpayList);
        return view('orders/index');
    }

    //创建订单并支付
    public function addOrder()
    {
        if (input(('request.'))) {
            //接收值
            $nCategoryId = input(('request.category_id'));
            if (empty($nCategoryId)) {
                return false;
            }

            $arrData['enterprise_id'] = \session('rootId');
            $type = \session('adminRole')->type;
            $arrData['type'] = $type;
            //没有登录 查询视频价格
            if (empty($arrData['enterprise_id'])) {
                return "<script> alert('请先登录~');parent.location.href='/'; </script>";
            }
            $objSignup = new OrdersModel();
            $signupMap = array('enterprise_id' => $arrData['enterprise_id'], 'category_id' => $nCategoryId, 'type' => $type, 'order_status' => 1);
            $arrSignup = $objSignup->getSignupList($signupMap, 1);
            //已经购买
            if ($arrSignup) {
                return "<script> alert('您已购买~');parent.location.href='/qiyes'; </script>";
            }
            $arrMap = array('enterprise_id' => $arrData['enterprise_id'], 'category_id' => $nCategoryId, 'type' => $type, 'order_status' => 2);
            $arrSignupList = $objSignup->getSignupList($arrMap, 1);
//dd($arrSignupList);
            //订单已经创建
            if (!empty($arrSignupList)) {
                $enterprise_order = $arrSignupList['num'];
                $price = $arrSignupList['price'];
                $enterprise_name = $arrSignupList['enterprise_name'];
                $note = $arrSignupList['note'];
                $category_id = $arrSignupList['category_id'];
//                dd($enterprise_name);
                return $this->payOrder($enterprise_order, $price, $enterprise_name, $note, $category_id);

            } else {
                //订单未创建
                $arrData['category_id'] = $nCategoryId;

                $map['enterId'] = $arrData['enterprise_id'];
                $type = \session('adminRole')->type;
                $arrData['type'] = $type;
                $recordModel = new RecordModel;
                //获取详细信息
                $enterprise_name = $recordModel->BaseFind($map, array('firmname'));
                $arrData['enterprise_name'] = $enterprise_name->firmname;
                if (empty($arrData['enterprise_name']) || $arrData['enterprise_name'] == "查询结果为空") {
                    $arrData['enterprise_name'] = "环保Link企业";
                }
                $arrData['num'] = get_order_num(\session('rootId'));

                $objFlvModel = new FlvCategoryModel();
                $arrSignupLists = $objFlvModel->getSignupList(array('id' => $nCategoryId), 1);
                if ($type == 1) {
                    $arrData['price'] = $arrSignupLists['bag_price'];
                } else if ($type == 2) {
                    $arrData['price'] = $arrSignupLists['sign_price'];
                } else if ($type == 3) {
                    $arrData['price'] = $arrSignupLists['own_bag_price'];
                }
                $arrData['note'] = "直接付款";

                $OrdersModel = new OrdersModel();
                $res = $OrdersModel->addData($arrData);
                //订单创建成功
                if ($res) {
                    $enterprise_order = $arrData['num'];
                    $price = $arrData['price'];
                    $enterprise_name = $arrData['enterprise_name'];
                    $note = $arrData['note'];
                    $category_id = $arrData['category_id'];
                    return $this->payOrder($enterprise_order, $price, $enterprise_name, $note, $category_id);

                    //return "<script> alert('报名成功');parent.location.href='/qiyes?type=order'; </script>";
                } else {
                    return "<script> alert('报名失败');</script>";
                }
                //return "<script> alert('订单获取失败');parent.location.href='/qiyes?type=order'; </script>";
            }
        }
    }


    public function addbatch()
    {
        if (input(('request.'))) {
            //接收值
            $arrData = input('request.');
            $nCategoryId = $arrData['category_id'];
            $nuId = $arrData['uid'];
            if (empty($nCategoryId)) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }
            if (empty($nuId)) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }
            $academy_id = \session('rootId');
            $type = \session('adminRole')->type;
            $objOrders = new OrdersModel();
            $objFlvModel = new FlvCategoryModel();
            $recordModel = new RecordModel;
            $arrSignupLists = $objFlvModel->getSignupList(array('id' => $nCategoryId), 1);
            if ($type == 1) {
                $arrData['price'] = $arrSignupLists['bag_price'];
            } else if ($type == 2) {
                $arrData['price'] = $arrSignupLists['sign_price'];
            } else if ($type == 3) {
                $arrData['price'] = $arrSignupLists['own_bag_price'];
            }

            $where = [];
            foreach ($nuId as $k => $v) {
                $where[$k]['enterprise_id'] = $v;
                $where[$k]['type'] = 3;
                $where[$k]['num'] = get_order_num($v);
                $where[$k]['price'] = $arrData['price'];
                $where[$k]['add_time'] = date("Y-m-d H:i:s");
                $where[$k]['update_time'] = date("Y-m-d H:i:s");
                $where[$k]['order_status'] = 2;
                $where[$k]['pay'] = 0;
                $where[$k]['count'] = $k + 1;
                $num[] = $where[$k]['num'];
                $a = $recordModel->BaseFind(array("enterId" => $v), array('firmname'));
                if (empty($a) || $a == "查询结果为空") {
                    $a = "环保Link企业";
                }
                $where[$k]['enterprise_name'] = $a;
                $where[$k]['category_id'] = $nCategoryId;
                $where[$k]['role'] = $type;
                $where[$k]['pid'] = 0;
                $where[$k]['academy_id'] = $academy_id;
            }
//            print_r($num);die;
            $objData = $objOrders->BaseSaveAll($where);
            if ($objData) {
                $arrMsg['status'] = 1;
                $arrMsg['msg'] = "报名成功";
                $num = implode(",", $num);
                $arrMsg['num'] = $num;
                return $arrMsg;
            } else {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "报名失败";
                return $arrMsg;
            }
        }
    }

    //院校所有订单
    public function selbatch()
    {
        $academy_id = \session('rootId');
        if (empty($academy_id)) {
            return $this->success("非法操作", '/allbatch');
        }
        $OrdersModel = new OrdersModel;

        //获取订单
        $payMap = array('academy_id' => $academy_id, 'type' => 2, 'role' => 2, 'e.pid' => 0);
        $allOrder = $OrdersModel->getOrderJoinByFirmsId($payMap);
        foreach ($allOrder as $item) {
            if($item["order_status"]==2){
                if($item["epay"]==0){//未支付
                    $unpayList[] = $item;
                }else{//支付中
                    $payingList[]=$item;
                }
            }else{//已支付
                $payList[]=$item;
            }
        }
        $type = \session('adminRole')->type;
        $this->assign('type', $type);
        $this->assign('payList', $payList);
        $this->assign('unpayList', $unpayList);
        $this->assign('payingList', $payingList);
        return view('batch');

    }

    //批量缴费的页面 院校
    public function paybatch()
    {
        $academy_id = \session('rootId');
        if (empty($academy_id)) {
            return $this->success("非法操作", '/allbatch');
        }
        $OrdersModel = new OrdersModel;
        $allOrderPara = array('academy_id' => $academy_id, 'type' => 3);
        $allOrder = $OrdersModel->getOrderJoinByFirmsId($allOrderPara);

        foreach ($allOrder as $item) {
            if($item["order_status"]==2){
                if($item["epid"]==0){//未支付
                    $unpayList[] = $item;
                }else{//支付中
                    $payingList[]=$item;
                }
            }else{//已支付
                $payList[]=$item;
            }
        }
        $type = \session('adminRole')->type;
        $this->assign('type', $type);
        $this->assign('payList', $payList);
        $this->assign('unpayList', $unpayList);
        $this->assign('payingList', $payingList);
        return view('paybatch');

    }

    //合并订单详情信息
    public function selDetail()
    {
        $orderChild = [];
        if (input(('request.'))) {
            //接收值
            $num = input('request.')['num'];
            $type = \session('adminRole')->type;
            if ($type != 2 || empty($num)) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }
            $OrdersModel = new OrdersModel;
            $oneMap['num'] = $num;
            $OrderOne = $OrdersModel->BaseFind($oneMap);
            $Map['pid'] = $OrderOne['id'];
            if ($OrderOne) {
                $orderChild = $OrdersModel->selDetail($Map);
            }
        }
        $this->assign("orderChild", $orderChild);
        return view("orderChild");
    }

    //院校删除订单
    public function delDetail()
    {
        if (input(('request.'))) {
            //接收值
            $num = input('request.')['num'];
            $type = \session('adminRole')->type;
            if ($type != 2 || empty($num)) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }
            $OrdersModel = new OrdersModel;
            $OrderslogModel = new OrderslogModel;
            $oneMap['num'] = $num;
            $OrderOne = $OrdersModel->BaseFind($oneMap);
            if(!$OrderOne){
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "订单不存在或已删除！";
                return $arrMsg;
            }
            $OrderOne = $OrderOne->toArray();
            $Map['pid'] = $OrderOne['id'];
            if ($OrderOne) {
                unset($OrderOne['id']);
                $OrderOne['del_time'] = date("Y-m-d H:i:s");
                $OrderOne['uid'] = \session('rootId');
                $OrderOne['type'] = $type;
                $OrderSave = $OrderslogModel->BaseSave($OrderOne);
                if ($OrderSave) {
                    $OrderSaveChild = $OrdersModel->BaseUpdate(['pid' => 0, 'order_status' => 2], $Map);
                    $del = $OrdersModel->BaseDelete(['id' => $Map['pid']]);
                    if ($OrderSaveChild && $del) {
                        $arrMsg['status'] = 1;
                        $arrMsg['msg'] = "删除成功";
                        return $arrMsg;
                    } else {
                        $arrMsg['status'] = -1;
                        $arrMsg['msg'] = "删除失败";
                        return $arrMsg;
                    }
                }
            } else {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "无此订单";
                return $arrMsg;
            }

        }
    }

    //院校合并订单 并支付
    public function totalorder()
    {
        if (input(('request.'))) {
            //接收值
            $arrData = input('request.');
            $type = \session('adminRole')->type;
            $arrNuma = $arrData['arrnum'];
//            dd($arrNuma);

            if (is_string($arrNuma)) {
                $arrNum = explode(",", $arrNuma);
            } else {
                $arrNum = $arrNuma;
            }

            $academy_id = \session('rootId');
            if (empty($academy_id)) {
                return $this->success("非法操作", '/allbatch');
            }
            if (empty($arrNum)) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }
            $OrdersModel = new OrdersModel;
            $addData['enterprise_id'] = $academy_id;
            $addData['type'] = $type;
            $addData['num'] = get_order_num($academy_id);
            $addData['price'] = 0;
            $i = 0;
            $addData['category_id'] = '0';
            $addData['role'] = $type;
            $arrnums = [];
            foreach ($arrNum as $k => $v) {
                $arrnums[$k] = $OrdersModel->BaseFind(["num" => $v])->toArray();
                $addData['price'] += $arrnums[$k]['price'];
                $i++;
                $b = count(array_unset_tt($arrnums, "pid"));
                if ($arrnums[$k]['pid'] != 0 && $b != 1) {
                    return $this->success("订单不能二次合并,请重新选择", '/orders');
                }
            }
            $addData['add_time'] = date("Y-m-d H:i:s");
            $addData['update_time'] = date("Y-m-d H:i:s");
            $addData['order_status'] = 2;
            $addData['pay'] = 0;
            $addData['count'] = $i;
            $addData['enterprise_name'] = "环保Link企业";
            $addData['pid'] = 0;
            $addData['academy_id'] = $academy_id;
            $addId = $OrdersModel->BaseSave($addData);
//            dd($addId);

            if ($addId) {
                $map['update_time'] = date("Y-m-d H:i:s");
                $map['pid'] = $addId;
                foreach ($arrNum as $k => $v) {
                    $saveNum = $OrdersModel->saveOrderListByFirmNum(['num' => $v], $map);
                }
            }
            if ($saveNum) {
                $arrMsg['status'] = 1;
                $arrMsg['num'] = $addData['num'];
                $arrMsg['price']=$addData['price'];
                $arrMsg['msg'] = "订单合并成功";
                return $arrMsg;
            } else {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "订单合并失败";
                return $arrMsg;
            }
        }
    }

    //线下支付
    public function payment($strNums = '')
    {
        //接收值
        $arrData = input('request.');
        $type = \session('adminRole')->type;
        if (!empty($strNums)) {
            $strNum = $strNums;
        } else {
            $strNum = $arrData['num'];
        }
        $academy_id = \session('rootId');
        if (empty($academy_id)) {
            return $this->success("非法操作", '/allbatch');
        }
        if (empty($strNum)) {
            $arrMsg['status'] = -1;
            $arrMsg['msg'] = "非法操作";
            return $arrMsg;
        }
        $OrdersModel = new OrdersModel;
        $id = $OrdersModel->BaseFind(["num" => $strNum])['id'];
        if ($id) {
            $objOrder = $OrdersModel->saveOrderListByFirmNum("id=$id or pid=$id", ['pay' => 3]);
            if ($objOrder) {
                return $this->success("操作成功,请尽快完成汇款！", '/orders');
            } else {
                return $this->success("操作成功,请尽快完成汇款！", '/orders');
            }
        }


    }

    //线上支付
    public function onlinepay($strNums = '')
    {
        //接收值
        $arrData = input('request.');
        $type = \session('adminRole')->type;
        if (!empty($strNums)) {
            $strNum = $strNums['num'];
        } else {
            $strNum = $arrData['num'];
        }
//                dd($strNum);

        $academy_id = \session('rootId');
        if (empty($academy_id)) {
            return $this->success("非法操作", '/allbatch');
        }
        if (empty($strNum)) {
            $arrMsg['status'] = -1;
            $arrMsg['msg'] = "非法操作";
            return $arrMsg;
        }
        $OrdersModel = new OrdersModel;
        //获取合并后的订单信息
        $arrOrderData = $OrdersModel->BaseFind(["num" => $strNum]);
//        dd($arrOrderData);

        $enterprise_order = $arrOrderData['num'];
        $price = $arrOrderData['price'];
        $enterprise_name = $arrOrderData['enterprise_name'];
        $note = $arrOrderData['note'];
        $category_id = $arrOrderData['category_id'];
        return $this->payOrder($enterprise_order, $price, $enterprise_name, $note, $category_id);
    }


    public function qrcode()
    {
        error_reporting(E_ERROR);
        Loader::import('WxPay.example.phpqrcode.phpqrcode', EXTEND_PATH, '.php');
        //require_once 'phpqrcode/phpqrcode.php';
        $url = urldecode($_GET["data"]);
        $qrcode = new \QRcode();
        $qrcode->png($url);
    }


    //$enterprise_order 订单号
    //$price  价格
    //$enterprise_name  企业名称
    //$note  订单备注
    public function payOrder($enterprise_order, $price, $enterprise_name, $note = "", $category_id = 0)
    {

        header("Content-type:text/html;charset=utf-8");
//        header("Content-type:text/html;charset=utf-8");

// ******************************************************配置 start*************************************************************************************************************************
//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner'] = config('orders.partner');
//收款支付宝账号
        $alipay_config['seller_email'] = config('orders.sellerEmail');
//安全检验码，以数字和字母组成的32位字符
        $alipay_config['key'] = config('orders.key');
//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
//签名方式 不需修改
        $alipay_config['sign_type'] = strtoupper('MD5');
//字符编码格式 目前支持 gbk 或 utf-8
//$alipay_config['input_charset']= strtolower('utf-8');
//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
//$alipay_config['cacert']    = getcwd().'\\cacert.pem';
//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = config('orders.transport');
// ******************************************************配置 end*************************************************************************************************************************

// ******************************************************请求参数拼接 start*************************************************************************************************************************
        $parameter = array(
            "service" => config('orders.service'),
            "partner" => $alipay_config['partner'], // 合作身份者id
            "seller_email" => $alipay_config['seller_email'], // 收款支付宝账号
            "payment_type" => config('orders.paymentType'), // 支付类型
            "notify_url" => config('orders.notifyUrl'),
            "return_url" => config('orders.returnUrl'),
            "out_trade_no" => $enterprise_order, // 商户网站订单系统中唯一订单号
            "subject" => $enterprise_name, // 订单名称
            "total_fee" => $price, // 付款金额
            "body" => $note, // 订单描述 可选
            "show_url" => config('orders.show_url'), // 商品展示地址 可选
            "anti_phishing_key" => config('orders.antiPhishingKey'), // 防钓鱼时间戳  若要使用请调用类文件submit中的query_timestamp函数
            "exter_invoke_ip" => config('orders.exterInvokeIp'), // 客户端的IP地址
            "_input_charset" => config('orders.InputCharset'), // 字符编码格式
        );
//        dd($parameter);
// 去除值为空的参数
        foreach ($parameter as $k => $v) {
            if (empty($v)) {
                unset($parameter[$k]);
            }
        }
// 参数排序
        ksort($parameter);
        reset($parameter);

// 拼接获得sign
        $str = "";
        foreach ($parameter as $k => $v) {
            if (empty($str)) {
                $str .= $k . "=" . $v;
            } else {
                $str .= "&" . $k . "=" . $v;
            }
        }
        $parameter['sign'] = md5($str . $alipay_config['key']);    // 签名
        $parameter['sign_type'] = $alipay_config['sign_type'];
        $parameter['category_id'] = $category_id;

        $zfbparameter = $parameter;
//        dd($parameter);
        unset($zfbparameter['category_id']);
        $this->assign('zfbparameter', $zfbparameter);
        $this->assign('parameter', $parameter);
        return view('pays');
    }

    //微信支付查询订单支付状态（实现同步回调）
    public function wxNotifyUrl()
    {
        if ($arrData = input(('request.'))) {
//            dd($arrData);
            ini_set('date.timezone', 'Asia/Shanghai');
            error_reporting(E_ERROR);
            Loader::import('WxPay.lib.WxPay', EXTEND_PATH, '.Api.php');
            Loader::import('WxPay.example.log', EXTEND_PATH, '.php');
            $logHandler = new \CLogFileHandler("./logs/" . date('Y-m-d') . '.log');
            $log = \Log::Init($logHandler, 15);
            if (isset($_REQUEST["transaction_id"]) && $_REQUEST["transaction_id"] != "") {
                $transaction_id = $_REQUEST["transaction_id"];
                $input = new \WxPayOrderQuery();
                $input->SetTransaction_id($transaction_id);
                $data = \WxPayApi::orderQuery($input);
                echo json_encode($data);
                exit();
            }

            if (isset($_REQUEST["out_trade_no"]) && $_REQUEST["out_trade_no"] != "") {
                $out_trade_no = $_REQUEST["out_trade_no"];
                $input = new \WxPayOrderQuery();
                $input->SetOut_trade_no($out_trade_no);
                $data = \WxPayApi::orderQuery($input);
                echo json_encode($data);
                exit();
            }

        }
    }

}