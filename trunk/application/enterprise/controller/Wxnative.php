<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/6/11
 * Time: 18:20
 */

namespace app\enterprise\controller;

use app\common\controller\EnterBase;
use think\Loader;

class Wxnative extends EnterBase{

    public $dataUrl = "";
    public $dataNo = "";
    public function index()
    {
        if ($arrData = input(('request.'))) {
            Loader::import('WxPay.lib.WxPay', EXTEND_PATH, '.Api.php');
            Loader::import('WxPay.example.WxPay', EXTEND_PATH, '.NativePay.php');
            Loader::import('WxPay.example.WxPay', EXTEND_PATH, '.Api.php');
            $input = new \WxPayUnifiedOrder();
            $notify = new \NativePay();
            $input->SetBody($arrData['subject']);
            $input->SetAttach($arrData['body']);
            //订单号
            $input->SetOut_trade_no($arrData['out_trade_no']);
            $out_trade_no = $arrData['out_trade_no'];
//($arrData['total_fee'])*100
            $input->SetTotal_fee(($arrData['total_fee'])*100);  //分为单位
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("视频包");
            //设置异步回调地址
            $input->SetNotify_url(config("orders.wxnotifyUrl"));
            //原生扫码支付
            $input->SetTrade_type("NATIVE");
            //商品ID
            $input->SetProduct_id($arrData['category_id']);
            //生成二维码的短链接地址
            $result = $notify->GetPayUrl($input);
//            dd($input);
            if(empty($result["code_url"])){
                return "<script>alert('".$result['err_code_des'].",请联系商家');parent.location.href='/qiyes'; </script>";
            }else{
                $url2 = $result["code_url"];
            }
            $arr['url2'] = $url2;
            $this->dataUrl = $url2;
            $arr['out_trade_no'] = $out_trade_no;
            $this->dataNo = $out_trade_no;
        }
    }


}
$wxN = new Wxnative;
$wxN->index();

?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" type="text/css" href="/static/css/wxpay.css" />
    <title>微信支付</title>
</head>

<body>
<div class="container">
    <header style="display: none;" id="payHeader">
        <div class="header1" id="bignav">
            <div class="nav1">
                <div>
                    欢迎来到环保Link网络培训系统！
                </div>
                <div>
                    客服电话：400-070-1000
                </div>
            </div>
        </div>
        <div class="nav">
            <div class="nav_a">
                <div id="logo">
                    <img src="/static/img/wxpay/logo.png" />
                </div>
            </div>

        </div>
    </header>
    <div class="content_main">
        <div class="main">
            <div class="pay_main">
                <div class="pay_main_left"><img src="/static/img/wxpay/phone.png" /></div>
                <div class="pay_main_right">
                    <div class="pay_main_right_text">
                        <div><img alt="模式二扫码支付" src="http://www.elink.etledu.com/qrcode?data=<?php echo $wxN->dataUrl;?>" style="width:150px;height:150px;"/></div>
                    </div>
                    <div class="pay_main_right_fk"><div id="myDiv" style="font-weight: bold;font-size:18px;">欢迎使用微信支付</div>微信扫码&nbsp;向我付款</div>
                    <div class="pay_main_right_btn" id="btnBack" onclick="history.back();">返回</div>
                </div>
            </div>

        </div>

    </div>
</div>
<footer style="display: none;" id="payFooter">
    <P>意见反馈-用户协议-关于我们-商务合作-加入我们</P>
    <P>@北京博奥网络教育科技股份有限公司 技术支持 京 ICP 备16030848 号</P>
</footer>
</body>

</html>
<script src="/static/js/jquery.js" type="text/javascript" charset="utf-8"></script>

<script>
    if(parent.location.href.indexOf("/qiyes")<0){
        $("#payHeader").show();
        $("#payFooter").show();
    }
    //同步实现页面的效果展示
    //设置每隔5000毫秒执行一次load() 方法
    myIntval=setInterval('load()',5000);
    function load(){
        //document.getElementById("timer").innerHTML=parseInt(document.getElementById("timer").innerHTML)+1;
        if (window.XMLHttpRequest)
        {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {
            // code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange=function(){
            // console.info(xmlhttp);

            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                //订单响应信息
                result=JSON.parse(xmlhttp.responseText);
                //alert(trade_state);
                //alert(trade_state);
                if(result.trade_state=='SUCCESS')
                {
                    document.getElementById("myDiv").innerHTML='支付成功,跳转中...';
                    document.getElementById("btnBack").style.display="none";

                    //alert(transaction_id);
                    //延迟3000毫秒执行tz() 方法
                    clearInterval(myIntval);
                    setTimeout("location.href='/wxNotify?out_trade_no='+result.out_trade_no+'&total_fee='+result.total_fee+'&transaction_id='+result.transaction_id+'&time_end='+result.time_end",3000);

                }
                else if(result.trade_state=='REFUND')
                {
                    document.getElementById("myDiv").innerHTML='转入退款';
                    clearInterval(myIntval);
                }
                else if(result.trade_state=='NOTPAY')
                {
                    document.getElementById("myDiv").innerHTML='请扫码支付';

                }
                else if(result.trade_state=='CLOSED')
                {
                    document.getElementById("myDiv").innerHTML='已关闭';
                    clearInterval(myIntval);
                }
                else if(result.trade_state=='REVOKED')
                {
                    document.getElementById("myDiv").innerHTML='已撤销';
                    clearInterval(myIntval);
                }
                else if(result.trade_state=='USERPAYING')
                {
                    document.getElementById("myDiv").innerHTML='用户支付中';
                }
                else if(result.trade_state=='PAYERROR')
                {
                    document.getElementById("myDiv").innerHTML='支付失败';
                    clearInterval(myIntval);
                }

            }
        };
        //orderquery.php 文件返回订单状态，通过订单状态确定支付状态
        xmlhttp.open("POST","/wxNotifyUrl",false);
        //下面这句话必须有
        //把标签/值对添加到要发送的头文件。
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send("out_trade_no=<?php echo $wxN->dataNo;?>");

    }
</script>