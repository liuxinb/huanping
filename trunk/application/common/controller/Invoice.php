<?php
/**
 * Created by PhpStorm.
 * User: zhuying
 * Date: 2018/7/8
 * Time: 14:41
 * 发票
 */

namespace app\common\controller;
use app\common\model\Draw as DrawCommon;
use think\Request;

class Invoice
{
    //院校发票修改
    public function savedraw(Request $request)
    {
        $adminId = session('rootId');
        $type = \session('adminRole')->type;
        $dataArray = $request->param();
        //验证发票名称
        if (!preg_match('/^[\(\)\x{4e00}-\x{9fa5}A-Z_]+$/u', $dataArray['invoice_name'])) {
            return "发票名称不符合规则，只支持中文，大写英文，英文小括弧和下划线";
        }
        //验证发票名称
        if (!preg_match('/^((\d{6}[0-9A-Z]{9})|([0-9A-Za-z]{2}\d{6}[0-9A-Za-z]{10})|([0-9A-Za-z]{20}))$/', $dataArray['identification'])) {
            return "纳税人识别号不正确";
        }
        $dataDraw['invoice_name'] = $dataArray['invoice_name'];
        $dataDraw['identification'] = $dataArray['identification'];
        $dataDraw['phone'] = $dataArray['phone'];
        $dataDraw['address'] = $dataArray['address'];
        $dataDraw['bank'] = $dataArray['bank'];
        $dataDraw['number'] = $dataArray['number'];
//dd($dataArray);
        $DrawCommonmodel = new DrawCommon();
        $result = $DrawCommonmodel->BaseUpdate($dataDraw,['uid'=>$adminId,'type'=>2]);

        if ($result) {
            return '保存成功';
        } else {
            return '尚未做修改操作';
        }

    }
    /// </summary>
    /// <param name="id">电子发票表主键</param>
    /// <param name="serialCode">发票请求流水号</param>
    /// <param name="invoiceTitle">发票抬头</param>
    /// <param name="schoolnsrsbh">纳税人识别号</param>
    /// <param name="totalMoney">金额</param>
    /// <param name="orderDate">缴费汇总日期</param>
    /// <param name="email">邮箱</param>
    /// <param name="phone">电话</param>
    /// <param name="addressPhone"></param>
    /// <param name="bankNumber"></param>
    /// <returns></returns>
    public function  ApplyEInvoice($status,$serialCode,$invoiceTitle,$schoolnsrsbh, $totalMoney, $orderDate, $email, $phone, $addressPhone,$bankNumber)
    {
        if ( strlen( $serialCode )>20 || empty( $serialCode ) ) return false;
        if ( strlen( $invoiceTitle )>100 || empty( $invoiceTitle ) ) return false;
        if ( strlen( $schoolnsrsbh )>20 || empty( $schoolnsrsbh ) ) return false;
        if ( empty( $totalMoney ) ) return false;
        if ( empty( $orderDate ) ) return false;
        if ( isset( $email ) ) {
            if ( strlen( $email )>50 ) return false;
        }
        if ( isset( $phone ) ) {
            if ( strlen( $phone )>20 ) return false;
        }
        if ( isset( $addressPhone ) ) {
            if ( strlen( $addressPhone )>100 ) return false;
        }
        if ( isset( $bankNumber ) ) {
            if ( strlen( $bankNumber )>100 ) return false;
        }

        $xml = $this->XML($status,$serialCode,$invoiceTitle,$schoolnsrsbh, $totalMoney, strtotime($orderDate), $email, $phone, $addressPhone,$bankNumber);
        $aes = new \AES('N30FFtPQpjmmjx6H');
        $endata = $aes->encrypt($xml);
        return $this->returnData($endata);
    }

    public function AESEncryptRequest($xml){
        # 加密
        $aes = new \AES('N30FFtPQpjmmjx6H');
        $endata = $aes->encrypt($xml);
        return $this->webCURL($endata);
    }


    public function XML($status,$serialCode,$invoiceTitle,$schoolnsrsbh, $totalMoney, $orderDate, $email, $phone, $addressPhone,$bankNumber)
    {
//             $data['type'] = 1;

        $Kplx = config('invoice.Kplx');        //开票类型（0：蓝票，1：红票）
        $GmfNsrsbh = $schoolnsrsbh;  //购买方纳税人识别号
        $GmfMc = $invoiceTitle;   //购买方名称
        $GmfDzdh = $addressPhone;
        $GmfYhzh = $bankNumber;
        $YfpDm = config('invoice.YfpDm');            //原发票代码
        $YfpHm = config('invoice.YfpHm');                //原发票号码
        $Jshj = $totalMoney;
        $Bz = config('invoice.Bz');               //备注
        $Ddrq = date('YmdHis',$orderDate);
        $Kprq = date('YmdHis');             //开票日期
        $Ddh = config('invoice.Ddh');              //短码
        $XfzYx = $email;
        $XfzSjh = $phone;
        $XSF_NSRSBH = config('invoice.XSF_NSRSBH');  //销售方纳税人识别号
        $XsfYhzh = config('invoice.XsfYhzh');          //销售方银行账号
        $XsfDzdh = config('invoice.XsfDzdh');          //销售方地址、电话
        $XsfMc = config('invoice.XsfMc');            //销售方名称
        $Kpr = config('invoice.Kpr');            //开票人
        $Skr = config('invoice.Skr');            //收款人
        $Fhr = config('invoice.Fhr');            //复核人
        $sl = config('invoice.sl');             //税率
        $se = ($Jshj/1.03)*config('invoice.sl');            //税额
//        print_r($se);die;
        $SPMB = config('invoice.SPMB');          //商品编码
        $arr = [
            [
                'FPHXZ'=> config('invoice.FPHXZ'),    //发票行性质
                'HH'=> config('invoice.HH'),          //HH
                'XMMC'=> config('invoice.XMMC'),         //XMMC
                'DW'=> config('invoice.DW'),           //计量单位
                'GGXH'=> config('invoice.GGXH'),           //规格型号
                'XMJE'=>round($Jshj / (1 + $sl), 2),         //项目金额
                'SL'=>$sl,           //税率
                'SE'=>$se,           //税额
                'SN'=> config('invoice.SN'),           //商品SN号
                'SPBM'=>$SPMB,           //商品编码
                'YHZCBS'=> config('invoice.YHZCBS'),           //优惠政策标识
                'LSLBS'=> config('invoice.LSLBS'),            //零税率标识
                'ZZSTSGL'=> config('invoice.ZZSTSGL'),          //增值税特殊管理
                'XMSL'=> config('invoice.XMSL'),          //增值税特殊管理
                'XMDJ'=> config('invoice.XMDJ'),          //增值税特殊管理
            ]
        ];
        $hjje = round($Jshj / (1 + $arr[0]['SL']), 2);          //合计金额 此处视为所有税率一致，若不同需改动
        $hjse = round(($Jshj - $hjje),2);


        $str = '<?xml version="1.0" encoding="UTF-8"?>';

        $str .= "<BUSINESS ID='REQUEST_E_FAPIAO_KJ'>";
        $str .= "<KJXX>";
        $str .= "  <KPLX>" . $Kplx . "</KPLX>";
        $str .= "  <FPQQLSH>" . $serialCode . "</FPQQLSH>";
        $str .= "  <XSF_NSRSBH>" . $XSF_NSRSBH . "</XSF_NSRSBH>";
        $str .= "  <XSF_MC>" . $XsfMc . "</XSF_MC>";
        $str .= "  <XSF_DZDH>" . $XsfDzdh . "</XSF_DZDH>";
        $str .= "  <XSF_YHZH>" . $XsfYhzh . "</XSF_YHZH>";
        $str .= "  <GMF_NSRSBH>" . $GmfNsrsbh . "</GMF_NSRSBH>";
        $str .= "  <GMF_MC>" . $GmfMc . "</GMF_MC>";
        $str .= "  <GMF_DZDH>" . $GmfDzdh . "</GMF_DZDH>";
        $str .= "  <KPRQ>" . $Kprq . "</KPRQ>";
        $str .= "  <GMF_YHZH>" . $GmfYhzh . "</GMF_YHZH>";
        $str .= "  <KPR>" . $Kpr . "</KPR>";
        $str .= "  <SKR>" . $Skr . "</SKR>";
        $str .= "  <FHR>" . $Fhr . "</FHR>";
        $str .= "  <YFP_DM>" . $YfpDm . "</YFP_DM>";
        $str .= "  <YFP_HM>" . $YfpHm . "</YFP_HM>";
        $str .= "  <JSHJ>" . $Jshj . "</JSHJ>";
        $str .= "  <HJJE>" . $hjje . "</HJJE>";
        $str .= "  <HJSE>" . $hjse . "</HJSE>";
        $str .= "  <BZ>" . $Bz . "</BZ>";
        $str .= "  <DDRQ>" . $Ddrq . "</DDRQ>";
        $str .= "  <DDH>" . $Ddh . "</DDH>";
        $str .= "  <XFZ_YX>" . $XfzYx . "</XFZ_YX>";
        $str .= "  <XFZ_SJH>" . $XfzSjh . "</XFZ_SJH>";
        $str .= "</KJXX>";

        if (!empty($arr)){
            $str .= "<KJXXMX COUNT='".count($arr)."'>";
            foreach ($arr as $v){
                $str.='<KJMX>';
                $str.= '<FPHXZ>'.$v['FPHXZ'].'</FPHXZ>';
                $str.='<HH>'.$v['HH'].'</HH>';
                $str.='<XMMC>'.$v['XMMC'].'</XMMC>';
                $str.='<GGXH>'.$v['GGXH'].'</GGXH>';
                $str.='<DW>'.$v['DW'].'</DW>';
                $str.='<XMSL>'.$v['XMSL'].'</XMSL>';
                $str.='<XMDJ>'.$v['XMDJ'].'</XMDJ>';
                $str.='<XMJE>'.$v['XMJE'].'</XMJE>';
                $str.='<SL>'.$v['SL'].'</SL>';
                $str.='<SE>'.number_format($v['SE'], 2).'</SE>';
                $str.='<SN>'.$v['SN'].'</SN>';
                $str.= '<SPBM>'.$v['SPBM'].'</SPBM>';
                $str.= '<YHZCBS>'.$v['YHZCBS'].'</YHZCBS>';
                $str.= '<LSLBS>'.$v['LSLBS'].'</LSLBS>';
                $str.= '<ZZSTSGL>'.$v['ZZSTSGL'].'</ZZSTSGL>';
                $str.='</KJMX>';
            }
            $str .= '</KJXXMX>';
        }
        $str .= '</BUSINESS>';
//        print_r($str);die;
        return $str;
    }

    public function webCURL($xml)
    {
        if (!extension_loaded("curl")) {
            trigger_error("对不起，请开启curl功能模块！", E_USER_ERROR);
        }
        header("content-type:text/html; charset=utf8;");
        ini_set('date.timezone','Asia/Shanghai');
        //构造xml\
//        print_r($xml);die;
        if (empty($xml)) return false;


        $aes = new \AES('N30FFtPQpjmmjx6H');
        $xmldata = $aes->decrypt($xml);
//print_r($xmldata);die;

        //初始一个curl会话
        $curl = curl_init();

        //设置url
        curl_setopt($curl, CURLOPT_URL, config('invoice.url'));

        //设置发送方式：post
        curl_setopt($curl, CURLOPT_POST, true);

        //设置发送数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);

        //TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        //执行cURL会话 ( 返回的数据为xml )
        $return_xml = curl_exec($curl);
//print_r($return_xml);die;
        //关闭cURL资源，并且释放系统资源
        curl_close($curl);
        if ($return_xml){
            $dom = simplexml_load_string($return_xml);
            $content = $dom->data->content;
            $aes = new \AES('N30FFtPQpjmmjx6H');
            $xmlObj = $aes->decrypt($content);
            //解密后连接
            $xmls = simplexml_load_string($xmlObj, null, LIBXML_NOCDATA);
//            print_r($xmls);die;

            if ($xmls->RESULT->CODE == '0000')
            {
                $status = 1;
            }else{
                $status = -1;
            }
            $arr['status'] = $status;
            $arr['content'] = (array)$xmls->RESULT;
//            print_r($arr);die;
            return $arr;
        }else{
            return false;
        }

//        print_r($return_xml);die;
    }


    public function returnData($xml)
    {
        $appId = config('invoice.appId');
        $interfaceCode = config('invoice.interfaceCode');   //接口编码
        $requestCode = config('invoice.requestCode'); //数据交换请求发起方代码
        $requestTime = date("Y-m-d H:i:s");
        $responseCode = config('invoice.responseCode');     //数据交换请求接受方代码

        $returnXml = '<?xml version="1.0" encoding="utf-8"?>';
        $returnXml .= '<interface>';
        $returnXml .= '<globalInfo>';
        $returnXml .= '<appId>'.$appId.'</appId>';
        $returnXml .= '<interfaceCode>'.$interfaceCode.'</interfaceCode>';
        $returnXml .= '<requestCode>'.$requestCode.'</requestCode>';
        $returnXml .= '<requestTime>'.$requestTime.'</requestTime>';
        $returnXml .= '<responseCode>'.$responseCode.'</responseCode>';
        $returnXml .= '</globalInfo>';
        $returnXml .= '<returnstateInfo>';
        $returnXml .= '<returnCode/>';
        $returnXml .= '<returnMessage/>';
        $returnXml .= '</returnstateInfo>';
        $returnXml .= '<data>';
        $returnXml .= '<content>'.$xml.'</content>';
        $returnXml .= '<signature/>';
        $returnXml .= '</data>';
        $returnXml .= '</interface>';

        return $this->AESEncryptRequest($returnXml);

    }


}