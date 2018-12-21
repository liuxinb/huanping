<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/6/11
 * Time: 18:20
 */

namespace app\own\controller;
use app\common\model\EnterpriseOrder as OrdersModel;
use app\common\model\FlvCategory as FlvCategoryModel;
use think\Controller;
use think\Loader;
class Orders extends Controller
{
    public function zfbpay(){
        return $this->fetch();
    }


    //创建订单并支付
    public function addOrder(){
        if (input(('request.')))
        {
            //接收值
            $nCategoryId = input(('request.category_id'));
            if (empty($nCategoryId)){
                return false;
            }
            $uid = model("User")->getUidByphone(\session('user.phone'));
            $arrData['enterprise_id'] = $uid;
            $type = 3;

            $objSignup = new OrdersModel();
            $signupMap = array('enterprise_id'=>$arrData['enterprise_id'],'category_id'=>$nCategoryId,'type'=>$type,'order_status'=>1);

            $arrSignup = $objSignup->getSignupList($signupMap,1);

            //已经购买
            if ($arrSignup){
                return json(['error'=>true,'message'=>'您已经购买']);
            }

            $arrMap = array('enterprise_id' => $arrData['enterprise_id'], 'category_id' => $nCategoryId, 'type' => $type, 'order_status' => 2);
            $arrSignupList = $objSignup->getSignupList($arrMap, 1);
//            dd($arrSignupList);
            //订单已经创建
            if (!empty($arrSignupList)){
                $enterprise_order = $arrSignupList['num'];
                $price = $arrSignupList['price'];
                $enterprise_name = $arrSignupList['enterprise_name'];
                $note = $arrSignupList['note'];
                $category_id = $arrSignupList['category_id'];
//                dd($price);
                return $this->payOrder($enterprise_order,$price,$enterprise_name,$note,$category_id);

            }else{
                //订单未创建
                $arrData['category_id'] = $nCategoryId;

                $map['uid'] = $uid;
                $arrData['type'] = 3;
                $arrData['role'] = 3;
                //获取个人详细信息
                $arrData['enterprise_name'] = model("UserDetail")->BaseFind($map,array('name'))['name'];
                if (empty($arrData['enterprise_name']) || $arrData['enterprise_name'] =="查询结果为空"){
                    $arrData['enterprise_name'] = "环保Link企业";
                }

                $arrData['num'] = get_order_num(\session('rootId'));

                $objFlvModel = new FlvCategoryModel();
                //$arrSignupLists = $objFlvModel->getSignupList(array('id'=>$nCategoryId),1);
                $arrSignupLists = $objFlvModel->BaseFind(array('id'=>$nCategoryId), ['id', 'title', 'bag_img', 'sign_price', 'own_bag_price']);
                if ($type == 1){
                    $arrData['price'] = $arrSignupLists['bag_price'];
                }else if ($type ==2){
                    $arrData['price'] = $arrSignupLists['sign_price'];
                }else if ($type == 3){
                    $academy = model("User")->BaseFind(['id'=>$uid]);

                    $arrData['academy_id'] = $academy['enterprise_id'];


                    $count = model("CollegeRecord")->BaseSelectCount(['admin_id'=>$academy['enterprise_id'],"state"=>1]);
//                    dd($count);

                    if ($count > 0){
                        $arrData['price'] = $arrSignupLists['sign_price'];
                    }else{
                        $arrData['price'] = $arrSignupLists['own_bag_price'];

                    }
                }
                $arrData['note'] = "直接付款";
                $OrdersModel = new OrdersModel();
                $res = $OrdersModel->addData($arrData);
                //订单创建成功
                if ($res)
                {
                    $enterprise_order = $arrData['num'];
                    $price = $arrData['price'];
                    $enterprise_name = $arrData['enterprise_name'];
                    $note = $arrData['note'];
                    $category_id = $arrData['category_id'];
                    return $this->payOrder($enterprise_order,$price,$enterprise_name,$note,$category_id);

                    //return "<script> alert('报名成功');parent.location.href='/qiyes?type=order'; </script>";
                }else{
                    return "<script> alert('报名失败');</script>";
                }
                //return "<script> alert('订单获取失败');parent.location.href='/qiyes?type=order'; </script>";
            }
        }
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
    public function payOrder($enterprise_order,$price,$enterprise_name,$note="",$category_id=0){

        header("Content-type:text/html;charset=utf-8");
//        header("Content-type:text/html;charset=utf-8");

// ******************************************************配置 start*************************************************************************************************************************
//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']		= config('orders.partner');
//收款支付宝账号
        $alipay_config['seller_email']	= config('orders.sellerEmail');
//安全检验码，以数字和字母组成的32位字符
        $alipay_config['key']			= config('orders.key');
//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
//签名方式 不需修改
        $alipay_config['sign_type']    = strtoupper('MD5');
//字符编码格式 目前支持 gbk 或 utf-8
//$alipay_config['input_charset']= strtolower('utf-8');
//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
//$alipay_config['cacert']    = getcwd().'\\cacert.pem';
//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = config('orders.transport');
// ******************************************************配置 end*************************************************************************************************************************

// ******************************************************请求参数拼接 start*************************************************************************************************************************
        $parameter = array(
            "service" => config('orders.service'),
            "partner" => $alipay_config['partner'], // 合作身份者id
            "seller_email" => $alipay_config['seller_email'], // 收款支付宝账号
            "payment_type"	=> config('orders.paymentType'), // 支付类型
            "notify_url"	=> config('orders.notifyUrl'),
            "return_url"	=> config('orders.returnUrl'),
            "out_trade_no"	=> $enterprise_order, // 商户网站订单系统中唯一订单号
            "subject"	=> $enterprise_name."的订单", // 订单名称
            "total_fee"	=> $price, // 付款金额
            "body"	=> $note, // 订单描述 可选
            "show_url"	=> config('orders.show_url'), // 商品展示地址 可选
            "anti_phishing_key"	=> config('orders.antiPhishingKey'), // 防钓鱼时间戳  若要使用请调用类文件submit中的query_timestamp函数
            "exter_invoke_ip"	=> config('orders.exterInvokeIp'), // 客户端的IP地址
            "_input_charset"	=> config('orders.InputCharset'), // 字符编码格式
        );
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
        $parameter['sign'] = md5($str . $alipay_config['key']);	// 签名
        $parameter['sign_type'] = $alipay_config['sign_type'];
        $parameter['category_id'] = $category_id;

        $zfbparameter = $parameter;
        unset($zfbparameter['category_id']);
        $this->assign('zfbparameter',$zfbparameter);
        $this->assign('parameter',$parameter);
        return view('pays');
    }


    public function wxNotifyUrl()
    {
        if ( $arrData=input(('request.')))
        {
//            dd($arrData);
            ini_set('date.timezone','Asia/Shanghai');
            error_reporting(E_ERROR);
            Loader::import('WxPay.lib.WxPay', EXTEND_PATH, '.Api.php');
            Loader::import('WxPay.example.log', EXTEND_PATH, '.php');
            $logHandler= new \CLogFileHandler("./logs/".date('Y-m-d').'.log');
            $log = \Log::Init($logHandler, 15);
            if(isset($_REQUEST["transaction_id"]) && $_REQUEST["transaction_id"] != ""){
                $transaction_id = $_REQUEST["transaction_id"];
                $input = new \WxPayOrderQuery();
                $input->SetTransaction_id($transaction_id);
                $data=\WxPayApi::orderQuery($input);
                echo json_encode($data);
                exit();
            }

            if(isset($_REQUEST["out_trade_no"]) && $_REQUEST["out_trade_no"] != ""){
                $out_trade_no = $_REQUEST["out_trade_no"];
                $input = new \WxPayOrderQuery();
                $input->SetOut_trade_no($out_trade_no);
                $data=\WxPayApi::orderQuery($input);
                echo json_encode($data);
                exit();
            }

        }
    }


}