<?php
namespace app\enterprise\controller;
use app\common\controller\Invoice as InvoiceCommon;
use app\common\model\Draw as DrawCommon;
use app\common\model\Invoice as InvoiceModel;
use app\common\controller\EnterBase;
use app\common\model\EnterpriseOrder as OrdersModel;


class Invoice extends EnterBase
{
    /*
     * 发票列表页
     * */
    public function index()
    {
        $model = new InvoiceCommon();
        $xml = $model->ApplyEInvoice(2,"trade3123","燕郊有限责任公司","911101084578014680",1999,strtotime("2018-7-9"),"wq@qq.com","15810101010","010-12312312","62262004156099345");
        print_r($xml);
    }

    public function selinvoice(){
        $objDrawModel = new DrawCommon();
        $map['uid'] = \session('rootId');
        $map['type'] = \session('adminRole')->type;
        $num = input('request.arrnum');
        $arrData = $objDrawModel->BaseFind($map);
//        print_r(json_encode($arrData));die;
        if ($arrData){
            $return['status'] = 1;
            $return['msg'] = "获取成功";
            $arrData['num'] = $num;
            $return['arr'] = $arrData;
            return $return;
        }else{
            $return['status'] = -1;
            $return['msg'] = "获取失败";
            return $return;
        }
    }


    //开发票
    public function drawing(){
        header("content-type:text/html';charset=utf8");

        $num = empty($_GET['num'])?$_POST['num']:$_GET['num'];
        if(empty($num)){
            return false;
        }
        $objOrderModel = new OrdersModel();
        $type = \session('adminRole')->type;
        if ($type == 1){
            $objOrderDetails = $objOrderModel->getJoinRecordlist(['num'=>$num]);
        }else if($type == 2){
            //获取ID
            $id = session('rootId');
            //请求
            $addData = input('request.');
            $addData = $addData['d'];
//            dd($num);
            $addData['uid'] = $id;
            $type = \session('adminRole')->type;
            $addData['type'] = $type;
            $DrawCommon = new DrawCommon();
            $arrFindData = $DrawCommon->BaseFind(['uid'=>$id,'type'=>$type]);
            if (empty($arrFindData)){
                //验证发票名称
                if (!preg_match('/^[\(\)\x{4e00}-\x{9fa5}A-Z_]+$/u', $addData['invoice_name'])) {
                    $return['code'] = -2;
                    $return['msg'] = "发票名称不符合规则，只支持中文，大写英文，英文小括弧和下划线";
                    return $return;
                }
                //验证发票名称
                if (!preg_match('/^((\d{6}[0-9A-Z]{9})|([0-9A-Za-z]{2}\d{6}[0-9A-Za-z]{10})|([0-9A-Za-z]{20}))$/', $addData['identification'])) {
                    $return['code'] = -2;
                    $return['msg'] = "纳税人识别号不正确";
                    return $return;
                }
                $result = $DrawCommon->BaseSave($addData);
                if ($result) {
                    $price = $objOrderModel->BaseFind(['num'=>$num])['price'];

                    $objOrderDetails['invoicename'] = $addData['invoice_name'];
                    $objOrderDetails['identifynumber'] = $addData['identification'];
                    $objOrderDetails['price'] = $price;
                    $objOrderDetails['email'] = empty($addData['email'])?"1282198086@qq.com":$addData['email'];
                    $objOrderDetails['addressphone'] = $addData['phone'];
                    $objOrderDetails['invoiceaddress'] = $addData['address'];
                    $objOrderDetails['openingbank'] = $addData['bank'];
                    $objOrderDetails['accountnumber'] = $addData['number'];
//                    dd($objOrderDetails);

                } else {
                    return -1;
                }
            }else{
                $price = $objOrderModel->BaseFind(['num'=>$num])['price'];

                $objOrderDetails['invoicename'] = $arrFindData['invoice_name'];
                $objOrderDetails['identifynumber'] = $arrFindData['identification'];
                $objOrderDetails['price'] = $price;
                $objOrderDetails['email'] = empty($arrFindData['email'])?"1282198086@qq.com":$arrFindData['email'];
                $objOrderDetails['addressphone'] = $arrFindData['phone'];
                $objOrderDetails['invoiceaddress'] = $arrFindData['address'];
                $objOrderDetails['openingbank'] = $arrFindData['bank'];
                $objOrderDetails['accountnumber'] = $arrFindData['number'];
//                dd($objOrderDetails);
            }
        }
//        print_r($objOrderDetails);die;
        if (empty($objOrderDetails)){
            $arrReturn['code'] = -1;
            $arrReturn['pdf_url'] = '';
        }else{
            $model = new InvoiceCommon();
            $objInvoiceModel = new InvoiceModel();
            ini_set('date.timezone','Asia/Shanghai');
//            $num = "HP15300804969221";
            $map['num'] = $num;
            $InvoiceOne = $objInvoiceModel->BaseFind($map);
//            dd($InvoiceOne);
            if ($InvoiceOne){
                $arrReturn['code'] = 2;
                $arrReturn['pdf_url'] = $InvoiceOne['pdf_url'];
            }else{
//                dd($objOrderDetails);
                $xml = $model->ApplyEInvoice(2,$num,$objOrderDetails['invoicename'],$objOrderDetails['identifynumber'],$objOrderDetails['price'],date("Y-m-d"),$objOrderDetails['email'],$objOrderDetails['addressphone'],$objOrderDetails['invoiceaddress'].' '.$objOrderDetails['addressphone'],$objOrderDetails['openingbank'].' '.$objOrderDetails['accountnumber']);
//                dd($xml);
                //1 开票成功 2重复开票 -2开票失败
                if (!empty($xml['status']) && $xml['status'] == 1 || $xml['status'] == -1){
//echo 111;die;
                    $arrData['name'] = $objOrderDetails['invoicename'];
                    $arrData['address'] = $objOrderDetails['province'].$objOrderDetails['city'].$objOrderDetails['county'].$objOrderDetails['worksite'];
                    $arrData['bank'] = $objOrderDetails['openingbank'].' '.$objOrderDetails['accountnumber'];
                    $arrData['account'] = $objOrderDetails['invoiceaddress'].' '.$objOrderDetails['addressphone'];
                    $arrData['duty'] = $objOrderDetails['identifynumber'];
                    $arrData['phone'] = $objOrderDetails['addressphone'];
                    $arrData['price'] = $objOrderDetails['price'];
                    $arrData['ewm'] = $xml['content']['EWM'];
                    $arrData['enterId'] = \session('rootId');
                    $arrData['num'] = $num;
                    $arrData['jqbh'] = $xml['content']['JQBH'];
                    $arrData['fpdm'] = $xml['content']['FPDM'];
                    $arrData['fphm'] = $xml['content']['FPHM'];
                    $arrData['kprq'] = $xml['content']['KPRQ'];
                    $arrData['fp_mw'] = $xml['content']['FP_MW'];
                    $arrData['jym'] = $xml['content']['JYM'];
                    $arrData['dm'] = $xml['content']['DM'];
                    $arrData['pdf_url'] = $xml['content']['PDF_URL'];
                    $arrData['create_time'] = date("Y-m-d H:i:s");
//                    print_r($arrData);die;
                    $objAddData = $objInvoiceModel->BaseSave($arrData);
                    if($objAddData){
                        $arrReturn['code'] = 1;
                        $arrReturn['pdf_url'] = $xml['content']['PDF_URL'];
                    }else{
                        $arrReturn['code'] = -2;
                        $arrReturn['pdf_url'] = '';
                    }
                }else{
                    $arrReturn['code'] = -2;
                    $arrReturn['pdf_url'] = '';
                }
            }

        }
//        print_r($arrReturn);die;
        return $arrReturn;
    }

}