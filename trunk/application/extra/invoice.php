<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/7/10
 * Time: 9:51
 */

return  [
    'Kplx' => "0",       //开票类型（0：蓝票，1：红票）
    'YfpDm' => "",       //原发票代码
    'YfpHm' => "",       //原发票号码
    'BZ' => "",       //备注
//    'Kprq' => date('YmdHis'),       //开票日期
    'Ddh' => "",       //短码
    'XSF_NSRSBH' => '911101176876214860',  //销售方纳税人识别号
    'XsfYhzh' => '招商银行北京分行长安街支行 110929066110902',          //销售方银行账号
    'XsfDzdh' => '北京市怀柔区雁栖经济开发区杨雁路88号  010-56053669',          //销售方地址、电话
    'XsfMc' => '北京博奥网络教育科技股份有限公司',            //销售方名称
    'Kpr' => '李瑞',            //开票人
    'Skr' => '吴凯峰',            //收款人
    'Fhr' => '胡国英',            //复核人
    'sl' => 0.03,             //税率
    'SPMB' => '3070201020000000000',          //商品编码
    'FPHXZ'=>0,    //发票行性质
    'HH'=>1,          //HH
    'XMMC'=>'技术支持费',         //XMMC
    'DW'=>'',           //计量单位
    'GGXH'=>'',           //规格型号
    'SN'=>'',           //商品SN号
    'YHZCBS'=>'',           //优惠政策标识
    'LSLBS'=>'',            //零税率标识
    'ZZSTSGL'=>'',          //增值税特殊管理
    'XMSL'=>'',          //增值税特殊管理
    'XMDJ'=>'',          //增值税特殊管理
    'appId'=>'dzfp',    //appId
    'interfaceCode'=>'REQUEST_E_FAPIAO_KJ',    //接口编码
    'requestCode'=>'911101176876214860-aes',    //数据交换请求发起方代码
    'responseCode'=>'trade3',    //数据交换请求接受方代码

    //正式打开这个
    'url'=>"http://data.e-inv.cn/eInvoiceApi",          //接口地址

];