<?php

namespace app\api\controller;

use app\common\model\Exam;
use app\common\model\TestBase;
use app\common\model\User;
use app\common\model\UserPlan;
use think\Request;

/**
 * Class Test
 * @package app\api\controller
 */
class Test extends Pro
{
    /**
     * @var Exam
     */
    private $exam;
    /**
     * @var User
     */
    private $user;

    /**
     * Test constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->exam = new Exam();
        $this->user = new User();
    }

    /**
     * 考试结束提交考试结果
     * @param Request $request
     * @user 李海江 2018/7/17~下午4:08
     */
    public function index(Request $request)
    {
        $cid = $request->param('cid');
        if (!isset($cid) || empty($cid)) {
            //请选择要参加哪个考试
            result('40052');
        }

        $map = [
            'uid' => $this->user->getUid($request->header('token')),
            'cid' => $request->param('cid'),
        ];

        $res = $this->exam->showTestAllow($map);
        //还没有考试 请先参加考试
        if ($res['code'] == 3) result('40049');
        //您已经通过考试
        if ($res['code'] == 1) result('40051');

        if ($res['code'] == 2) {
            //接受app提交的结果
            $appResult = $request->param('answer');

            if (isset($appResult) && !empty($appResult)) {
                //APP json解码
                $appResultArr = json_decode($appResult, true);
                //WEB 数据库里json解码
                $webAnswer = json_decode($res['data']['answer'], true);
                $true = $false = 0;

                for ($i = 0; $i < count($appResultArr); $i++) {
                    str_replace(',', '', $appResultArr[$i]) == str_replace(',', '', $webAnswer[$i]) ? $true++ : $false++;
                }


                $arr['results'] = round($true / ($true + $false) * 100);
                $arr['results'] < config('exam.results') ? $arr['isqualified'] = 0 : $arr['isqualified'] = 1;
                $arr['user_answer'] = json_encode($appResult);

                if ($this->exam->BaseUpdate($arr, $map)) {
                    unset($arr['user_answer']);
                    Pro::$message['20024']['data'] = $arr;
                    result('20024');
                }
            } else {
                //考试提交异常 app没提交东西
                result('40050');
            }
        }

    }


    /**
     * 创建考试
     * @param Request $request
     * @user 李海江 2018/7/17~下午3:58
     */
    public
    function create(Request $request)
    {
        //选择哪门课程
        $cid = $request->param('cid');
        $uid = $this->user->getUid($request->header('token'));

        if (!isset($cid) || empty($cid)) {
            //请选择要参加哪个考试
            result('40052');
        }

        $res = $this->exam->showTestAllow(['uid' => $uid, 'cid' => $cid]);
        //您已经通过考试
        if ($res['code'] == 1) result('40051');

        $userPlan = new UserPlan();
        //参加考试前要看学没学完 如果学习结束才可以考试
        $myPlan = $userPlan->BaseFind(['uid' => $uid, 'pid' => 0, 'cid' => $cid]);
        if (empty($myPlan)) result('40053');

        if ($myPlan['type'] == 1 || $myPlan['complete'] >= '100') {

            $testBase = new TestBase();
            $field = ['id', 'content', 'option', 'answer'];
            $radioMap = ['type' => 1, 'movie_id' => 'cid' . $cid];
            $selectMap = ['type' => 2, 'movie_id' => 'cid' . $cid];
            $judgeMap = ['type' => 3, 'movie_id' => 'cid' . $cid];
            $answer = $answer_id = [];
            $order = 'rand()';

            $radioList = $testBase->BaseSelect($radioMap, $field, $order, config('exam.radio'));
            $selectList = $testBase->BaseSelect($selectMap, $field, $order, config('exam.select'));
            $judgeList = $testBase->BaseSelect($judgeMap, $field, $order, config('exam.judge'));

            $data = array_merge((array)$radioList, $selectList, $judgeList);


            foreach ($data as $k => $v) {
                $answer[] = $v['answer'];
                $answer_id[] = $v['id'];
            }


            foreach ($radioList as $k => $v) {
                unset($radioList[$k]['answer']);
                unset($radioList[$k]['id']);
                //题号
                $radioList[$k]['content'] = ($k + 1) . '.' . $radioList[$k]['content'];

                $radioOption = json_decode($radioList[$k]['option'], true);

                $radioOptionArr = array();
                foreach ($radioOption as $kk => $vv) {
                    if (!empty($vv)) {
                        $radioOptionArr[] = $kk . '.' . $vv;
                    }
                }

                $radioList[$k]['option'] = $radioOptionArr;
            }

            foreach ($selectList as $k => $v) {
                unset($selectList[$k]['answer']);
                unset($selectList[$k]['id']);
                //题号
                $selectList[$k]['content'] = ($k + 1) . '.' . $selectList[$k]['content'];
                $selectOption = json_decode($selectList[$k]['option'], true);
                $selectOptionArr = array();
                foreach ($selectOption as $kk => $vv) {
                    if (!empty($vv)) {
                        $selectOptionArr[] = $kk . '.' . $vv;
                    }
                }
                $selectList[$k]['option'] = $selectOptionArr;
            }


            foreach ($judgeList as $k => $v) {
                unset($judgeList[$k]['answer']);
                unset($judgeList[$k]['id']);
                //题号
                $judgeList[$k]['content'] = ($k + 1) . '.' . $judgeList[$k]['content'];
                $judgeOption = json_decode($judgeList[$k]['option'], true);
                $judgeOptionArr = array();
                foreach ($judgeOption as $kk => $vv) {
                    if (!empty($vv)) {
                        $judgeOptionArr[] = $kk . '.' . $vv;
                    }
                }
                $judgeList[$k]['option'] = $judgeOptionArr;
            }

            $app_data['0'] = ['title' => '一、单选题', 'data' => $radioList];
            $app_data['1'] = ['title' => '二、多选题', 'data' => $selectList];
            $app_data['2'] = ['title' => '三、判断题', 'data' => $judgeList];

            $arr['answer'] = json_encode($answer, true);
            $arr['answer_id'] = json_encode($answer_id, true);
            $arr['uid'] = $uid;
            $arr['cid'] = $cid;
            //创建试卷前查询有没有没通过的试卷 , 删掉

            //删除以前的不要的答题
            $this->exam->BaseDelete(['uid' => $uid, 'isqualified' => 0, 'cid' => $cid]);
            //入库
            if ($this->exam->BaseSave($arr)) {
                Pro::$message['20023']['data'] = $app_data;
                result('20023');
            } else {
                //创建失败 请重试
                result('40048');
            }
        } else {
            result('40053');
        }
    }
}
