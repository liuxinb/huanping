<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/3
 * Time: 下午4:43
 */

namespace app\api\controller;

use app\common\model\PromoteVideo;
use think\Request;

/**
 * Class FlvMovie
 * @package app\api\controller
 */
class Flvmovie extends Pro
{

    /**
     * @var PromoteVideo
     */
    private $obj;

    /**
     * FlvMovie constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->obj = new PromoteVideo();
    }


    /**
     * 获取试学列表 目前只有一个
     * @param Request $request
     * @user 李海江 2018/9/4~上午9:47
     */
    public function tryLearnList()
    {
        //获取数据
        $list = $this->obj->BaseFind(['state'=>1],['id','cover','title']);
        //给视频加url
        $list = addPath($list,'cover','odd');
        //返回数据
        Pro::$message['20013']['data'] = $list;
        result('20013');
    }


    /**
     * 试学
     * @param Request $request
     * @user 李海江 2018/9/3~下午9:32
     */
    public function tryLearn(Request $request)
    {
        //获取试学的视频id
        $id = $request->post('id');
        //如果为空
        if (empty($id)) result('40025');
        //表链接
        $join = array(['__TEA_TEACHER__ tea', 'movie.teacher_id=tea.id']);
        //获取的字段
        $field = ['vid','movie.title','tea.name','tea.path','referral','qg','content'];
        //获取数据
        $data = $this->obj->BaseJoinFind('movie', $join, ['movie.id' => $id],$field);
        //添加图片地址
        $data = addPath($data, 'path', 'odd');
        //返回
        Pro::$message['20013']['data'] = $data;
        result('20013');
    }
}