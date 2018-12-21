<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/3
 * Time: 下午1:50
 */

namespace app\api\controller;

use app\common\model\Recruitment;
use think\Request;

/**
 * Class Recruit
 * @package app\api\controller
 */
class Recruit extends Pro
{
    private $obj;

    /**
     * Recruit constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->obj = new Recruitment();
    }


    /**
     * 获取全部招聘信息
     * @user 李海江 2018/9/3~下午2:37
     */
    public function index()
    {
        //获取参数
        $appData = input('post.');
        //默认10条数据
        !empty($appData['count']) ?: $count = 10;
        //获取
        $list = $this->obj->BaseSelectPage([$count, true, ['page'=>$appData['page']]], ['state'=>1], ['id','company_name', 'work', 'size', 'experience', 'degree', 'wage', 'allow_date'], ['allow_date'=>'desc']);
        //返回
        Pro::$message['20025']['data'] = $list->toArray()['data'];
        result('20025');
    }

    /**
     * 招聘信息详情
     * @user 李海江 2018/9/3~下午3:10
     */
    public function show()
    {
        //获取招聘信息id
        $id = input('post.id');
        if (empty($id)) result('40055');

        $result = $this->obj->BaseFind(['id'=>$id]);
        unset($result['admin_id']);unset($result['state']);unset($result['id']);

        Pro::$message['20025']['data'] = $result;
        result('20025');

    }

    /**
     * 招聘信息搜索
     * @user 李海江 2018/9/4~下午5:37
     */
    public function search()
    {
        $search = input('post.search');
        $workData = $this->obj->query("select * from hp_recruitment  where work like '%".$search."%' or company_name like '%".$search."%'");
        foreach ($workData as $v){
            unset($v['admin_id']);unset($v['state']);
            $data[] = $v;
        }

        Pro::$message['20025']['data'] = $data;
        result('20025');
    }
}