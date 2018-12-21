<?php

namespace app\enterprise\controller;

use app\common\controller\EnterBase;
use think\Request;
use think\Session;
use app\common\model\EnterpriseOrder as OrderModel;
use app\common\model\FlvCategory as FlvCategoryModel;
use app\common\model\User as UserModel;

class Signup extends EnterBase
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function index()
    {
        $nEnterpriseId = \session('rootId');
        $objModel = new FlvCategoryModel();
        $map['pid'] = 0;
        //获取购买课程列表
        $arrSignupList = $objModel->getSignupList($map);
        $objOrderModel = new OrderModel();
        $type = \session('adminRole')->type;
        $arrMap = array('e.enterprise_id' => $nEnterpriseId,'type'=>$type);
//        dd($arrMap);
        //是否购买
        $firmOrders = $objOrderModel->getOrderListByFirmId($arrMap);
//        dd($firmOrders);

        foreach ($arrSignupList as $k => $v) {
            $arrSignupList[$k]['order_status'] = 0;
            foreach ($firmOrders as $item) {
                if ($v['id'] == $item['category_id']) {
                    $arrSignupList[$k]['order_status'] = $item['order_status'];
                    break;
                }
            }
        }
        $this->assign('signupList', $arrSignupList);
        return view('signup/index');

    }

    public function batch()
    {
        $objModel = new FlvCategoryModel();
        $map['pid'] = 0;
        //获取购买课程列表
        $arrSignupList = $objModel->getSignupList($map);
        // print_r($arrSignupList);die;
        $this->assign('signupList', $arrSignupList);
        return view();
    }

    public function allbatch()
    {
        if (Request::instance()->isGet())
        {
            $arrData = input();
//            print_r($arrData);die;
            if (empty($arrData['category']))
            {
                return $this->success('非法操作','/batch');
            }
            $user = \session('rootId');

            $objUserModel = new UserModel();
            $objOrderModel = new OrderModel();
            //user表  type = 1 是院校
            $userMap['enterprise_id'] = $user;
            $userMap['type'] = 1;
            $arrUser = $objUserModel->BaseWithSelect('adminUserDetails',$userMap);
            $i = 0;
            foreach ($arrUser as $k=>$v){
                $arrSet = $objOrderModel->BaseFind(['enterprise_id'=>$v['id'],'type'=>3]);
                $arrUser[$k]['order_status'] = "0";
                if ($arrSet){
                    $i++;
                    $arrUser[$k]['order_status'] = $arrSet->role;
                }

            }
            $objModel = new FlvCategoryModel();
            $map['id'] = $arrData['category'];
            $arrSignupList = $objModel->getSignupList($map);
            //返回
            $this->assign('signupList', $arrSignupList);
            $this->assign("arrUser",$arrUser);
            return view();
        }
    }


}