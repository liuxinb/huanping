<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\Auth_group;
use think\Db;
use think\Request;
use think\Validate;

/**
 *
 * 后台发票管理
 */
class Invoice extends AdminBase {
    /// <summary>
    /// 申请开票
    /// </summary>
    /// <returns></returns>
    /// <summary>
    /// 申请电子发票
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
public function Index()
{
    echo 1;
//    ApplyEInvoice(2,"trade2","燕郊有限责任公司","911101084578014680",1999,"2018-7-4","wq@qq.com","`15810101010","010-12312312","62262004156099345");
//return View();
}



}