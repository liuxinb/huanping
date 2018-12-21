<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/5/31
 * Time: 下午4:26
 */

namespace app\api\controller;

use think\Request;


/***
 * 首页
 * Class Index
 * @package app\api\controller
 */
class Course extends Pro
{
    //token
    private $token;
    //model
    private $obj;

    /***
     * 构造函数
     * Course constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->token = $request->header('token');
        $this->obj = model('flvCategory');
    }

    /***
     * 获取所有课程包
     */
    public function getAllCourse()
    {
        //get i need field
        $field = implode(config('appCourse'), ',');
        //send field then get all course
        $reslut = $this->obj->getAllCourse($field);
        if (!empty($reslut)) {
            //给url拼接上当前域名
            $reslut = addPath($reslut, 'bag_img');
            Pro::$message['20013']['data'] = $reslut;
            result('20013');
        } else {
            result('40029');
        }
    }

    /***
     * 展示一个视频包里的视频列表
     */
    public function showCourseList()
    {
        $token = request()->header('token');
        //验证token
        $user_status = model('User')->hasOneCount('token', $token)->status;
        //用户被冻结无法登陆
        if ($user_status == '-1') result('40018');
        //获取视频包的ID
        $id = request()->post('id');
        if (empty($id)) result('40025');
        //获取课程包的名称、id、价格
        $data['course_bag'] = $this->obj->getOneCourse($id);
        //获取展示课程需要展示的字段
        $field = implode(config('appCourseList'), ',');
        //获取视频包下的每个课程的信息
        $data['course_list'] = $this->obj->getCourseList($id, $token, $field);
        //返回数据
        Pro::$message['20014']['data'] = $data;
        result('20014');
    }

    /***
     * 显示学时信息
     * 需要传递学时id
     */
    public function showCourse()
    {
        $token = request()->header('token');
        //获取视频课程ID
        $id = request()->post('vid');
        //如果id为空返回错误
        if (empty($id)) result('40025');
        //执行查找数据
        $data = $this->obj->showCourse($id, config('appOneCourse'), config('teacher'), $token);

        $data['teacher'] = addPath($data['teacher'], 'path', 'odd');

        Pro::$message['20013']['data'] = $data;
        result('20013');
    }


    /***
     * 获取当前视频的所有评价
     */
    public function showEvaluation()
    {
        $mid = request()->param('mid');
        $page = request()->param('page');
        $count = request()->param('count');

        $data = $this->obj->getEvaluation($mid, $page, $count);

        if (empty($data)) {
            result('40044');
        } else {
            Pro::$message['20022']['data'] = $data;
            result('20022');
        }
    }
}