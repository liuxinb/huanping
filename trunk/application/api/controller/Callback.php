<?php
/**
 * Created by PhpStorm.
 * User: zhuying
 * Date: 2018/7/8
 * Time: 14:41
 * 发票
 */

namespace app\api\controller;

use app\common\model\EnterpriseOrder as OrdersModel;
use app\common\model\FlvCategory as FlvCategoryModel;
use app\common\model\OrderPay as OrderPayModel;
use app\common\model\Record as RecordModel;
use app\common\model\CollegeRecord as CollegeRecordModel;
use think\Controller;
use think\Session;

class Callback extends Controller
{
    //支付宝同步回调
    //支付宝异步回调
    public function notify()
    {
        if (input(('request.'))) {             //如果$_POST数据不为空的话
            //接收值
            $_POST = input(('request.'));
            if (!empty($_POST['trade_status'])) {         //状态值不为空
                $bill_list_id_date = $_POST['out_trade_no'];        //商户订单号
                $trade_no = $_POST['trade_no'];                     //支付宝交易号
                $trade_status = $_POST['trade_status'];             //交易状态
                $total_fee = $_POST['total_fee'];                   //支付金额
                //检查该账单是否已支付.....
                if ($trade_status == 'TRADE_FINISHED' OR $trade_status == 'TRADE_SUCCESS') {
                    $map['num'] = $bill_list_id_date;
                    //处理你的业务逻辑......
                    $objOrdersModel = new OrdersModel();
                    $arrOrderOne = $objOrdersModel->getOrderListByFirmNum(array("num" => $bill_list_id_date), 1);
                    if ($arrOrderOne["pid"] == 0) {
                        $type = $arrOrderOne["type"];
                    }
                    //如果没有修改过
                    if ($arrOrderOne["order_status"] == 2) {
                        //修改订单状态
                        if ($arrOrderOne["price"] == floatval($total_fee)) {
                            $objOrdersModel->BaseUpdate(array("order_status" => 1, "update_time" => date("Y-m-d H:i:s"), "pay" => 1), $map);
                            if ($type == 2) {
                                $objOrdersModel->saveOrderListByFirmNum(['pid' => $arrOrderOne["id"]], array("order_status" => 1, "update_time" => $_POST['notify_time'], "pay" => 4));
                            }
                            $arrData['ordernum'] = $bill_list_id_date;
                            $arrData['pay'] = 1;
                            $arrData['price'] = $total_fee;
                            $arrData['tradenum'] = $trade_no;
                            $arrData['paytime'] = $_POST['notify_time'];
                            $arrData['buyer_email'] = $_POST['buyer_email'];
                            $arrData['buyer_id'] = $_POST['buyer_id'];
                            $objOrderPay = new OrderPayModel();
                            $objOrderPay->addOrderListByFirmNum($arrData);
                        }
                    }
                    if ($type == 3) {
                        $this->redirect("/own?payed=1");
                    } else {
                        $this->redirect("/close?tv=1");
                    }
                }
            }
        }
    }


    //微信同步&异步
    public function wxNotify()
    {
        $type = \session('adminRole')->type ? \session('adminRole')->type : Session::get('user.type');
        $isAjax = empty($type);

        if (!$isAjax) {
            $getData = input(('request.'));
            $orderNum = $getData['out_trade_no'];
            $trainNo = $getData['transaction_id'];
            $orderPrice = intval($getData["total_fee"]) / 100;
            $payTime = subTime($getData['time_end']);
        } else {
            //微信返回的数据
            $input = file_get_contents("php://input");
            if ($input) {
                $xml = simplexml_load_string($input);
                $orderNum = (string)$xml->out_trade_no;
                $trainNo = (string)$xml->transaction_id;
                $orderPrice = intval((string)$xml->total_fee) / 100;
                $payTime = subTime((string)$xml->time_end);
            }
        }
        ini_set('date.timezone', 'Asia/Shanghai');
        error_reporting(E_ERROR);

        $objOrdersModel = new OrdersModel();
        $map['num'] = $orderNum;
        $objSignup = new OrdersModel();
        $payOrder = $objSignup->getSignupList(array('num' => $orderNum, 'price' => $orderPrice), 1);

        if ($payOrder) {

            if (empty($type) && $payOrder["pid"] == 0) {
                $type = $payOrder["type"];
            }
            if ($type) {
                //是否支付
                if ($payOrder['order_status'] == 2) {
                    //修改订单状态
                    $saveOrder = $objOrdersModel->BaseUpdate(array("order_status" => 1, "update_time" => date("Y-m-d H:i:s"), "pay" => 2), $map);
                    if ($saveOrder) {
                        if ($type == 2) {
                            $objOrdersModel->saveOrderListByFirmNum(['pid' => $payOrder['id']], array("order_status" => 1, "update_time" => date("Y-m-d H:i:s"), "pay" => 4));
                        }
                        $orderPay['ordernum'] = $orderNum;
                        $orderPay['pay'] = 2;
                        $orderPay['price'] = $orderPrice;
                        $orderPay['tradenum'] = $trainNo;
                        $orderPay['paytime'] = $payTime;
                        $objOrderPay = new OrderPayModel();
                        $objOrderPay->BaseSave($orderPay);
                    }
                }
                if ($isAjax) {
                    echo "Success";
                } else {
                    if ($type == 3) {
                        $this->redirect("/own?payed=1");
                    } else {
                        $this->redirect("/orders?payed=1");
                    }
                }
            }
        }
        echo $isAjax ? "Success" : "<script>alert('非法操作,支付失败');parent.location.href='/'; </script>";
    }

}