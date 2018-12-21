<?php

namespace app\admin\controller;

use app\common\controller\Adminbase;
use app\common\model\Order_order;
use app\common\model\FlvCategory; //视频单价
use app\common\model\EnterpriseOrder; //企业订单表
use app\common\model\Order_category;
use app\common\model\EnterpriseUserdetail; //用户姓名，联系电话
use app\common\model\OrderPay;
use think\Db;
use think\Request;
use think\Validate;
use think\Controller;
use think\response\Json;

/**
 * 订单控制器
 */
class Order extends Adminbase {

    public $pay = array(0 => '--', 1 => '支付宝', 2 => '微信',3=>'线下支付');
    public $state = array( 1 => '已付款', 2 => '待付款');
    public $type = array(1 => '企业', 2 => '院校', 3 => '个人');

    protected $EnterpriseOrder;
    protected $OrderPay;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->EnterpriseOrder=new EnterpriseOrder();
        $this->OrderPay=new OrderPay();
    }




    
    //----------------------------------------------------------------------
             //    企业订单
    //-------------------------------------------------------------
//    企业订单列表
    public  function enterprise_index(){
            //  接收表单值
            $data=  Request::instance()->param();
            $map = array();
             //获取查询条件
             if(isset($data['keyword']) || isset($data['order_status']) || isset($data['email']) || isset($data['Payment'])|| isset($data['start_time'])|| isset($data['end_time'])|| isset($data['type'])){
                $map['e.num']  = array('like', '%'.input('keyword').'%');
                $map['e.order_status']  = array('like', '%'.input('order_status').'%');
                $map['f.email']  = array('like', '%'.input('email').'%');
                $map['e.pay']  = array('like', '%'.input('Payment').'%');
                $map['e.type']  = array('like', '%'.input('type').'%');
                 //如果不是空 根据支付时间段搜索  开始时间为空时默认时间是1970-1-1 结束时间为空时默认时间是当前时间
                 if(!empty($data['start_time'])||!empty($data['end_time'])){
                     $startTime=date("Y-m-d H:i:s",strtotime($data['start_time']));
                     $endTime=($data['end_time'])?date("Y-m-d 23:59:59",strtotime($data['end_time'])):date("Y-m-d H:i:s",time());
                     $map['op.paytime']=  array('between', [$startTime,$endTime]);
                 }
            }
           //订单所有数据
            $data= $this->EnterpriseOrder->ShowEnterpriseData($map);
           //渲染数据
           return view('',['all'=>$data,'Pay'=>$this->pay,'State'=>$this->state,'Type'=>$this->type]);

    }
    
//    企业订单详情
    public function enterprise_detail_order($id){
           //获取数据详情
            $object =$this->EnterpriseOrder->detailEnterpriseData($id);
           //渲染数据
            return view('',['Pay'=>$this->pay,'State'=>$this->state,'One'=>$object]);
    }

    //批量删除
    public function batchDelete($ids){
        $result = $this->EnterpriseOrder->destroy($ids);
        if($result){
            return json(["success"=>"删除成功","ids"=>$result]);
        }else{
            return json(["success"=>"删除失败,您没有权限!","ids"=>$result]);
        }

    }

    //批量下载订单
    public function exportcvs(){
        $data=  Request::instance()->param();
        $map=[];
        if ($data['type'] == 0){
            $this->output($this->EnterpriseOrder->NotShowEnterpriseData($map));
        }else{
                if ($data['type'] == 1){
                    if(empty($data['ids'])){
                        $this->error('请选择要导出的数据','enterprise_index');
                    }
                    $map = array(
                        'e.id'=>array('IN',$data['ids']),
                    );
                    $this->output($this->EnterpriseOrder->NotShowEnterpriseData($map));
                }
        }
    }

    /**
     * @param $object  从数据库导出的资源
     */
    private function output($object)
    {
        $output = '"企业订单号","企业名称","订购产品","成交单价","订购数量","联系电话","企业账号","详细地址","备注","下单时间","支付方式","订单状态","支付平台成交价格","交易流水号","支付时间","订单角色"'."\n";
        if ($object) {
            foreach ($object as $value) {
                $output .= "\"\t".$value['num']."\",\"\t".$value['enterprise_name']."\",\"\t".$value['title']."\",\"\t"."{$value['price']}"."\",\"\t".$value['count']."\",\"\t".$value['addressphone']."\",\"\t".$value['email']."\",\"\t".($value['province'].' '.$value['city'] .' '. $value['county'].' '.$value['registersite'] )."\",\"\t".($value['note']?$value['note']:'')."\",\"\t".$value['add_time']."\",\"\t".($value['pay'] ?$this->pay[$value['pay']]:'')."\",\"\t".($value['order_status']?$this->state[$value['order_status']]:'冻结')."\",\"\t".($value['payprice']?$value['payprice']:'')."\",\"\t".($value['tradenum']? $value['tradenum'] : '')."\",\"\t".($value['paytime']? $value['paytime'] : '')."\",\"\t".($value['type']? $this->type[$value['type']]:'')."\"\n";
            }
        }
        //  把 UTF-8  编码字串转换成 GBK 编码字串
        $output = mb_convert_encoding($output, 'GBK', 'UTF-8');
        downFile($output, 'order_' . time() . '.csv');
        exit;
    }

    protected $msg = [
        'tradenum' => '交易流水号格式不正确 ,请确认!',
    ];

    /**
     * 修改为线下支付
     *
     */
    public function orderEdit(){
        if(Request::instance()->isPost()){
            $data=input('post.');
            //验证订单流水号
            $validate = new Validate([
                'tradenum' => 'number',
            ], $this->msg);
            $datess['tradenum'] = $data['tradenum'];
            if (!$validate->check($datess)) {
                return $this->error($validate->getError(), 'enterprise_index');
            }

            //更新订单表数据
            $map=$data['num'];
            $updataData['order_status']='1';
            $updataData['pay']='3';
            //添加订单流程表数据
            $addData['ordernum']=$map;
            $addData['pay']='3';
            $addData['price']=$data['price'];
            $addData['tradenum']=$data['tradenum'];
            $addData['paytime']=date("Y-m-d H:i:s",time());
//            dump($map);dump($updataData);dump($addData);die;
            $request=$this->EnterpriseOrder->saveOrderListByFirmNum(['num'=>$map],$updataData);
            if($request){
                $request=$this->OrderPay->addOrderPayData($addData);
                if($request){
                    $this->success("添加成功","enterprise_index");
                }else{
                    $this->error("添加失败","enterprise_index");
                }
            }else{
                $this->error("添加失败","enterprise_index");
            }

        }
    }

    /*
     * 删除或驳回企业订单
     */
        public function recycle($id) {
            $map = array(
                'id' => $id
            );
            if ($map) {
                // $Order =new Order_order;
                $result =$this->EnterpriseOrder->delEnterpriseData($map);
                if ($result) {
                    $this->success('冻结成功,订单已冻结', 'Admin/Order/enterprise_index'); //未回收站路径
                } else {
                    $this->error('订单冻结失败或已冻结！',"Admin/Order/enterprise_index");
                }
            } else {
                $this->error('非法操作！',"home/index/index");
            }
        }



    //----------------------------------------------------------------------
    //    个人订单
    //-------------------------------------------------------------

    public $perPay = array(0 => '--', 1 => '支付宝', 2 => '微信',3=>'线下支付',4=>'批量支付');
    public $perState = array( 1 => '已付款', 2 => '待付款');
    public $perType = array(2 => '院校', 3 => '个人');

        /**
         * 个人订单
         *
         */
        public function person_index(){
            //  接收表单值
            $data=  Request::instance()->param();
            $map = array();
            //获取查询条件
            if(isset($data['pid']) ||isset($data['keyword']) || isset($data['order_status']) || isset($data['email']) || isset($data['Payment'])|| isset($data['start_time'])|| isset($data['end_time'])|| isset($data['type'])){
                $map['e.num']  = array('like', '%'.input('keyword').'%');
                $map['e.order_status']  = array('like', '%'.input('order_status').'%');
                $map['u.phone']  = array('like', '%'.input('email').'%');
                $map['e.pay']  = array('like', '%'.input('Payment').'%');
                $map['e.type']  = array('like', '%'.input('type').'%');
                $map['e.pid']  = array('like', '%'.input('pid').'%');
                //如果不是空 根据支付时间段搜索  开始时间为空时默认时间是1970-1-1 结束时间为空时默认时间是当前时间
                if(!empty($data['start_time'])||!empty($data['end_time'])){
                    $startTime=date("Y-m-d H:i:s",strtotime($data['start_time']));
                    $endTime=($data['end_time'])?date("Y-m-d 23:59:59",strtotime($data['end_time'])):date("Y-m-d H:i:s",time());
                    $map['op.paytime']=  array('between', [$startTime,$endTime]);
                }
            }
            //查询个人数据
            $data= $this->EnterpriseOrder->ShowPeoSchoolData($map);
            //渲染数据
            return view('',['all'=>$data,'Pay'=>$this->perPay,'State'=>$this->perState,'Type'=>$this->perType]);

        }

    //    院校个人订单详情
    public function person_detail_order($id){
        //获取数据详情
        $object =$this->EnterpriseOrder->detailPeoSchoolData($id);
        //渲染数据
        return view('',['Pay'=>$this->pay,'State'=>$this->perState,'One'=>$object]);
    }

    /*
  * 删除或驳回企业订单
  */
    public function proRecycle($id) {
        $map = array(
            'id' => $id
        );
        if ($map) {
            // $Order =new Order_order;
            $result =$this->EnterpriseOrder->delEnterpriseData($map);
            if ($result) {
                $this->success('冻结成功,订单已冻结', 'Admin/Order/person_index'); //未回收站路径
            } else {
                $this->error('订单冻结失败或已冻结！',"Admin/Order/person_index");
            }
        } else {
            $this->error('非法操作！',"home/index/index");
        }
    }


    //批量下载订单
    public function proExportcvs(){
        $data=  Request::instance()->param();
        $map=[];
        if ($data['type'] == 0){
            $this->outputPro($this->EnterpriseOrder->NotShowProSchoolData($map));
        }else{
            if ($data['type'] == 1){
                if(empty($data['ids'])){
                    $this->error('请选择要导出的数据','person_index');
                }
                $map = array(
                    'e.id'=>array('IN',$data['ids']),
                );
                $this->outputPro($this->EnterpriseOrder->NotShowProSchoolData($map));
            }
        }
    }

    /**
     * @param $object  从数据库导出的资源
     */
    private function outputPro($object)
    {
        $output = '"订单号","订购产品","成交单价","订购数量","手机号","备注","下单时间","支付方式","订单状态","支付平台成交价格","交易流水号","支付时间","订单角色","企业名称"'."\n";
        if ($object) {
            foreach ($object as $value) {
                $output .= "\"\t".$value['num']."\",\"\t".$value['title']."\",\"\t".$value['price']."\",\"\t"."{$value['count']}"."\",\"\t".$value['phone']."\",\"\t".($value['note']?$value['note']:'')."\",\"\t".$value['add_time']."\",\"\t".($value['pay']?$this->schPay[$value['pay']]:'')."\",\"\t".($value['order_status']?$this->schState[$value['order_status']]:'冻结')."\",\"\t".($value['payprice']?$value['payprice']:'')."\",\"\t".($value['tradenum'] ? $value['tradenum'] : '')."\",\"\t".($value['paytime']?$value['paytime']:'')."\",\"\t".($value['type']?$this->schType[$value['type']]:'')."\",\"\t".($value['enterprise_name'])."\"\n";

            }
        }

        //  把 UTF-8  编码字串转换成 GBK 编码字串
        $output = mb_convert_encoding($output, 'GBK', 'UTF-8');
        downFile($output, 'order_' . time() . '.csv');
        exit;
    }

    /**
     * 修改为线下支付
     *
     */
    public function proOrderEdit(){
        if(Request::instance()->isPost()){
            $data=input('post.');
            //验证订单流水号
            $validate = new Validate([
                'tradenum' => 'number',
            ], $this->msg);
            $datess['tradenum'] = $data['tradenum'];
            if (!$validate->check($datess)) {
                return $this->error($validate->getError(), 'person_index');
            }
            //更新订单表数据
            $map=$data['num'];
            $updataData['order_status']='1';
            $updataData['pay']='3';
            //添加订单流程表数据
            $addData['ordernum']=$map;
            $addData['pay']='3';
            $addData['price']=$data['price'];
            $addData['tradenum']=$data['tradenum'];
            $addData['paytime']=date("Y-m-d H:i:s",time());
//            dump($map);dump($updataData);dump($addData);die;
            $request=$this->EnterpriseOrder->saveOrderListByFirmNum(['num'=>$map],$updataData);
            if($request){
                $request=$this->OrderPay->addOrderPayData($addData);
                if($request){
                    $this->success("添加成功","person_index");
                }else{
                    $this->error("添加失败","person_index");
                }
            }else{
                $this->error("添加失败","person_index");
            }

        }
    }


    //----------------------------------------------------------------------
    //    院校订单
    //-------------------------------------------------------------

    public $schPay = array(0 => '--', 1 => '支付宝', 2 => '微信',3=>'线下支付',4=>'批量支付');
    public $schState = array(1 => '已付款', 2 => '待付款');
    public $schType = array(2 => '院校');

    /**
     * 院校订单
     *
     */
    public function school_index(){
        //  接收表单值
        $data=  Request::instance()->param();
        $map = array();
        //获取查询条件
        if(isset($data['keyword']) || isset($data['order_status']) || isset($data['email']) || isset($data['Payment'])|| isset($data['start_time'])|| isset($data['end_time'])|| isset($data['type'])){
            $map['e.num']  = array('like', '%'.input('keyword').'%');
            $map['e.order_status']  = array('like', '%'.input('order_status').'%');
            $map['f.email']  = array('like', '%'.input('email').'%');
            $map['e.pay']  = array('like', '%'.input('Payment').'%');
            $map['e.type']  = array('like', '%'.input('type').'%');
            //如果不是空 根据支付时间段搜索  开始时间为空时默认时间是1970-1-1 结束时间为空时默认时间是当前时间
            if(!empty($data['start_time'])||!empty($data['end_time'])){
                $startTime=date("Y-m-d H:i:s",strtotime($data['start_time']));
                $endTime=($data['end_time'])?date("Y-m-d 23:59:59",strtotime($data['end_time'])):date("Y-m-d H:i:s",time());
                $map['op.paytime']=  array('between', [$startTime,$endTime]);
            }
        }
        //查询个人数据
        $data= $this->EnterpriseOrder->ShowSchoolData($map);
        //渲染数据
        return view('',['all'=>$data,'Pay'=>$this->schPay,'State'=>$this->schState,'Type'=>$this->schType]);

    }

    //    院校个人订单详情
    public function school_detail_order($id){
        //获取数据详情
        $object =$this->EnterpriseOrder->detailSchoolData($id);
        //渲染数据
        return view('',['Pay'=>$this->schPay,'State'=>$this->schState,'One'=>$object]);
    }

    /*
  * 删除或驳回企业订单
  */
    public function schRecycle($id) {
        $map = array(
            'id' => $id
        );
        if ($map) {
            // $Order =new Order_order;
            $result =$this->EnterpriseOrder->delEnterpriseData($map);
            if ($result) {
                $this->success('冻结成功,订单已冻结', 'Admin/Order/school_index'); //未回收站路径
            } else {
                $this->error('订单冻结失败或已冻结！',"Admin/Order/school_index");
            }
        } else {
            $this->error('非法操作！',"home/index/index");
        }
    }


    //批量下载订单
    public function schExportcvs(){
        $data=  Request::instance()->param();
        $map=[];
        if ($data['type'] == 0){
            $this->outputSch($this->EnterpriseOrder->NotShowSchoolData($map));
        }else{
            if ($data['type'] == 1){
                if(empty($data['ids'])){
                    $this->error('请选择要导出的数据','school_index');
                }
                $map = array(
                    'e.id'=>array('IN',$data['ids']),
                );
                $this->outputSch($this->EnterpriseOrder->NotShowSchoolData($map));
            }
        }
    }

    /**
     * @param $object  从数据库导出的资源
     */
    private function outputSch($object)
    {
        $output = '"订单号","成交单价","订购数量","手机号","备注","下单时间","支付方式","订单状态","支付平台成交价格","交易流水号","支付时间","订单角色","企业名称"'."\n";
        if ($object) {
            foreach ($object as $value) {
                $output .= "\"\t".$value['num']."\",\"\t".$value['price']."\",\"\t"."{$value['count']}"."\",\"\t".$value['email']."\",\"\t".($value['note']?$value['note']:'')."\",\"\t".$value['add_time']."\",\"\t".($value['pay']?$this->schPay[$value['pay']]:'')."\",\"\t".($value['order_status']?$this->schState[$value['order_status']]:'冻结')."\",\"\t".($value['payprice']?$value['payprice']:'')."\",\"\t".($value['tradenum'] ? $value['tradenum'] : '')."\",\"\t".($value['paytime']?$value['paytime']:'')."\",\"\t".($value['type']?$this->schType[$value['type']]:'')."\",\"\t".($value['enterprise_name'])."\"\n";

            }
        }
        //  把 UTF-8  编码字串转换成 GBK 编码字串
        $output = mb_convert_encoding($output, 'GBK', 'UTF-8');
        downFile($output, 'order_' . time() . '.csv');
        exit;
    }

//    /**
//     * @param $object  从数据库导出的资源
//     */
//    private function outputSch($object)
//    {
//        $output = '"订单号","订购产品","成交单价","订购数量","手机号","备注","下单时间","支付方式","订单状态","支付平台成交价格","交易流水号","支付时间","订单角色","企业名称"';
//        if ($object) {
//            foreach ($object as $value) {
//                $output .= "\r\n" . '"' . $value['num'] . '","' . $value['title']. '","' . $value['price'] . '元","' . $value['count'] . '","' . $value['email'] . '","'  . ($value['note']?$value['note']:'') . '","'. $value['add_time'] .'"," ' . ($value['pay']?$this->schPay[$value['pay']]:'') . '","' . ($value['order_status']?$this->schState[$value['order_status']]:'冻结') . '","' . ($value['payprice']?$value['payprice']:'') . '","' . ($value['tradenum'] ? $value['tradenum'] : '') . '","' . ($value['paytime']?$value['paytime']:'') . '","'.($value['type']?$this->schType[$value['type']]:'') .'","'. ($value['enterprise_name']) .'",';
//            }
//        }
//        //  把 UTF-8  编码字串转换成 GBK 编码字串
//        $output = mb_convert_encoding($output, 'GBK', 'UTF-8');
//        downFile($output, 'order_' . time() . '.csv');
//        exit;
//    }

    /**
     * 院校修改为线下支付
     *
     */
    public function schOrderEdit(){
        if(Request::instance()->isPost()){
            $data=input('post.');
//            $payOrder=$this->EnterpriseOrder->findOrderListByFirmNum(['num'=>$data['num']]);
            //根据订单号查询订单数据
            $num=$data['num'];
            $payOrder=$this->EnterpriseOrder->findJoinRecordlist(['num'=>$num]);
            if ($payOrder['type'] == 2 && $payOrder['pay']==3) {
                //修改企业订单状态
                $typeRes=$this->EnterpriseOrder->saveOrderListByFirmNum(['pid' => $payOrder['id']], array("order_status" => 1, "update_time" => date("Y-m-d H:i:s"), "pay" => 4));
                if($typeRes==true){
                    $shRes=$this->EnterpriseOrder->saveOrderListByFirmNum(['num' => $num], array("order_status" => 1, "update_time" => date("Y-m-d H:i:s"), "pay" => 4));
                    if($shRes){
                        //添加订单流程表数据
                        $addData['ordernum']=$num;
                        $addData['pay']='3';
                        $addData['price']=$data['price'];
                        $addData['tradenum']=$data['tradenum'];
                        $addData['paytime']=date("Y-m-d H:i:s",time());
                        //插入流水号
                        $shRes=$this->OrderPay->addOrderPayData($addData);
                        if($shRes){
                            $this->success("审核成功","school_index");
                        }else{
                            $this->error("审核失败","school_index");
                        }
                    }
                }else{
                    $this->error("审核失败","school_index");
                }
            }else{
                $this->error("审核失败","school_index");
            }
            //更新订单表数据
//            $map=$data['num'];
//            $updataData['order_status']='1';
//            $updataData['pay']='3';
//            //添加订单流程表数据
//            $addData['ordernum']=$map;
//            $addData['pay']='3';
//            $addData['price']=$data['price'];
//            $addData['tradenum']=$data['tradenum'];
//            $addData['paytime']=date("Y-m-d H:i:s",time());
////            dump($map);dump($updataData);dump($addData);die;
//            $request=$this->EnterpriseOrder->saveOrderListByFirmNum(['num'=>$map],$updataData);
//                if($typeRes){
//                    $this->success("审核成功","school_index");
//                }else{
//                    $this->error("审核失败","school_index");
//                }
        }
    }

    //合并订单详情信息
    public function selDetail($id)
    {
        $orderChild = [];
        if (input(('request.'))) {
            //接收值
//            $num = input('request.')['id'];
//            $type = \session('adminRole')->type;
//            if ($type != 2 || empty($num)) {
//                $arrMsg['status'] = -1;
//                $arrMsg['msg'] = "非法操作";
//                return $arrMsg;
//            }
//            $oneMap['num'] = $num;
//            $OrderOne = $this->EnterpriseOrder->BaseFind($num);
            $Map['pid'] = $id;
            $orderChild =$this->EnterpriseOrder->selDetail($Map);
        }
        $this->assign("orderChild", $orderChild);
        return view("orderChild");
    }


}
