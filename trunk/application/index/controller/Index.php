<?php

namespace app\index\controller;

use app\common\model\FlvCategory;
use app\common\model\PlugList as IndexModel;
use app\common\model\FlvCategory as FlvCategoryModel;
use app\common\model\FirmsignSurvey as FirmsignSurvey;
use app\common\model\Firmsign as Firmsign;
use app\common\model\FirmHope as FirmHopeModel;
use app\common\controller\EnterBase;
use app\common\model\Record as RecordModel;
use think\Cookie;
use think\Controller;

class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $userId = \session('rootId');
        if (empty($userId)) {
            $rootCookie = Cookie::get('rootId');
            $rootCookieN = Cookie::get('rootName');
            if (!empty($rootCookie)) {
                \session('rootId', $rootCookie);
                \session('rootName', $rootCookieN);
            }
        }
    }
//首页
    public function index()
    {
        header("Content-type:text/html;charset=utf-8");
        //轮播图
        $objModel = new  IndexModel();
        $plug['plug_typeid'] = 1;
        $plug['plug_status'] = 1;
        $arrPlugList = $objModel->getList($plug, array("plug_name", "plug_pic", "plug_url"));
        //课程 老师

//        dd($arrPlugList);
        $objFlvCategory = new FlvCategoryModel();
        $arrCategoryList = $objFlvCategory->getListJoin();
        $parentArray = $objModel->getListForeach($arrCategoryList);


        //问卷
        $objFirmsignSurveyModel = new FirmsignSurvey();
        $arrSurveyList = $objFirmsignSurveyModel->getList('', array("question", "A", "B", "C", "D", "type"));
        if (\session('rootId')) {
            $map['id'] = \session('rootId');
            $objFirmsignModel = new Firmsign();

            $arrEmail = $objFirmsignModel->getList($map, "email", "find");
            $this->assign('arrEmail', $arrEmail);

        }

        $this->assign('arrPlugList', $arrPlugList);
        $this->assign('parentArray', $parentArray);
        $this->assign('arrSurveyList', $arrSurveyList);
        return $this->fetch();
    }

    public function questionnaire()
    {
        if (input(('request.'))) {
            //接收值
            $arrAnswer = input(('request.'));
            //意见
            $arrDataCon['content'] = $arrAnswer['content'];
            if (empty($arrDataCon['content'])) {
                return "<script>alert('请答完题目');parent.location.href='/index'; </script>";
            }
            $objFirmsignSurveyModel = new FirmsignSurvey();
            $arrSurveyList = $objFirmsignSurveyModel->getList('', array("question", "A", "B", "C", "D", "id", "countA", "countB", "countC", "countD", "type"));

            $objFirmHopeModel = new FirmHopeModel();
            $objContent = $objFirmHopeModel->addOne($arrDataCon);

            //修改答案回答个数
            for ($i = 0; $i < count($arrSurveyList); $i++) {
                if (empty($arrAnswer["answer$i"])) {
                    return "<script>alert('请答完题目');parent.location.href='/index'; </script>";
                }
                $objFirmsignSurveyModel = new FirmsignSurvey();
                if ($arrSurveyList[$i]['type'] == 1) {
                    switch ($arrAnswer["answer$i"]) {
                        case 1 :
                            $map['id'] = $arrSurveyList[$i]['id'];
                            $objAnswer = $objFirmsignSurveyModel->saveOne($map, "countA");
                            break;
                        case 2:
                            $map['id'] = $arrSurveyList[$i]['id'];
                            $objAnswer = $objFirmsignSurveyModel->saveOne($map, "countB");
                            break;
                        case 3:
                            $map['id'] = $arrSurveyList[$i]['id'];
                            $objAnswer = $objFirmsignSurveyModel->saveOne($map, "countC");
                            break;
                        case 4:
                            $map['id'] = $arrSurveyList[$i]['id'];
                            $objAnswer = $objFirmsignSurveyModel->saveOne($map, "countD");
                            break;

                    }
                } else {
                    foreach ($arrAnswer["answer$i"] as $k => $v) {
                        switch ($arrAnswer["answer$i"][$k]) {
                            case 1 :
                                $map['id'] = $arrSurveyList[$i]['id'];
                                $objAnswer = $objFirmsignSurveyModel->saveOne($map, "countA");
                                break;
                            case 2:
                                $map['id'] = $arrSurveyList[$i]['id'];
                                $objAnswer = $objFirmsignSurveyModel->saveOne($map, "countB");
                                break;
                            case 3:
                                $map['id'] = $arrSurveyList[$i]['id'];
                                $objAnswer = $objFirmsignSurveyModel->saveOne($map, "countC");
                                break;
                            case 4:
                                $map['id'] = $arrSurveyList[$i]['id'];
                                $objAnswer = $objFirmsignSurveyModel->saveOne($map, "countD");

                                break;

                        }
                    }

                }
            }
            return "<script>parent.location.href='/#content_buttom_a'; </script>";
        }
    }

    //企业合作须知静态页
    public function cooper()
    {
        return view('login/cooper');
    }

    //帮助页
    public function help()
    {
        return view('login/help');
    }

    public function shop()
    {
        $objFlvCategory = new FlvCategory();
        $allCategory = $objFlvCategory->getData(["display"=>1]);
        $parentArray = [];
        foreach ($allCategory as $item) {
            if ($item['pid'] == 0) {
                array_push($parentArray, $item);
            }
        }
        foreach ($parentArray as $key => $parent) {
            $parentArray[$key]["childs"] = [];
            foreach ($allCategory as $item) {
                if ($item['pid'] == $parent["id"]) {
                    $parentArray[$key]["childs"][] = $item;
                }
            }
            if ($parentArray[$key]["childs"] == []) {
                unset($parentArray[$key]);
            }
        }
        $this->assign("parentArray", $parentArray);
        return view('login/shop');
    }

    public function close(){
        $type = $this->request->param("tv");
        $this->assign("type",$type);
        return $this->fetch();
    }
}
