<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/5/31
 * Time: 下午3:33
 */

namespace app\common\model;
/**
 * 课程包
 * Class Course
 * @package app\api\model
 */
class FlvCategory extends Base
{

    /**
     * 模型关联
     * @return \think\model\Relation
     * @user 李海江 2018/9/8~上午9:32
     */
    public function teacher()
    {
        return $this->hasOne('TeaTeacher','id','teacher_id');
    }


    /**
     * 获取所有课程包信息
     * @param $field
     * @param bool $list
     * @param string $cid
     * @return mixed
     * @user lihaijiang  2018/7/11 下午5:46
     */
    function getAllCourse($field, $list = true, $cid = '')
    {
        $where['pid'] = 0;
        $where['display'] = 1;
        if (!empty($cid)) {
            $where['id'] = $cid;
        }

        $data = $this
            ->field($field)
            ->where($where)
            ->order('create_time', 'desc')
            ->select();

        //以及每个视频包下的课程数量
        for ($i = 0; $i < count($data); $i++) {
            if ($list) {
                $count = $this
                    ->where('pid', $data[$i]->id)
                    ->count();
                $data[$i]['count'] = $count;
            } else {
                $data[$i]['allcount'] = $this->getAllCourseCount($data[$i]->id);
            }
        }
        return $data;
    }

    /***
     * 获取课程包下的课时总数
     * @param int $id 包id
     * @return int 课时数
     */
    function getAllCourseCount($id)
    {
        //获取该包下面的列表
        $data = $this
            ->field('id')
            ->where('pid', $id)
            ->select();
        $num = 0;
        //获取列表下的视频个数
        for ($i = 0; $i < count($data); $i++) {
            $count = $this->getMovie()
                ->where('category', $data[$i]->id)
                ->count();
            //相加
            $num += $count;
        }
        //返回个数
        return $num;
    }

    function getMovie()
    {
        return $this->hasMany('flv_movie', 'category', 'id');
    }

    /**
     * 视频列表页需要的视频包信信
     * @param $id    视频包id
     * @return mixed 视频包信息
     */
    function getOneCourse($id)
    {
        //课程包的标题
        $data = $this
            ->field('title')
            ->find($id);

        return $data;
    }

    /**
     * 展示对应课程里的课程信息
     * @param $id    课程包id
     * @param $field 课程列表需要展示的字段
     * @return mixed 返回课程列表需要展示信息
     */
    function getCourseList($id, $token, $field = '')
    {
        $uid = model('User')->getUid($token);
        $data = $this
            //视频列表需要展示的字段
            ->field($field)
            //获取该父类下的子类
            ->where('pid', $id)
            ->select();
        for ($i = 0; $i < count($data); $i++) {
            $video = $this->getMovie()
                ->field('id,title,hour')
                ->where('category', $data[$i]->id)
                ->select();
            //获取课程的教师信息
            $a = model('flv_movie')
                ->alias('m')
                ->join(config('database.prefix') . 'tea_teacher t', 'm.teacher_id=t.id', 'left')
                ->field('t.name teacher_name')
                ->where('m.category', $data[$i]->id)
                ->find();
            if (!empty($a)) {
                $data[$i] = array_merge($data[$i]->toArray(), $a->toArray());
            } else {
                $data[$i]['teacher_name'] = null;
            }
            for ($j = 0; $j < count($video); $j++) {
                //进度/分钟*60秒-5
                $shichang = $this
                    ->hasMany('UserPlan', 'uid', 'jid')
                    ->where('uid', $uid)
                    ->where('mid', $video[$j]->id)
                    ->find()['progress'];
                $progre = $shichang / ($video[$j]->hour);

                if ($progre < 0) {
                    $progre = 0;
                } elseif ($progre > 1) {
                    $progre = 1;
                }
                unset($video[$j]['hour']);
                $video[$j]['progre'] = floor($progre * 100) . "%";
            }
            $data[$i]['video'] = $video;
        }
        return $data;
    }


    /**
     * 获取视频信息
     * @param $id
     * @param string $field
     * @param $teacher_field
     * @param $token
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user lihaijiang  2018/7/11 下午6:11
     */
    function showCourse($id, $field = '', $teacher_field, $token)
    {
        //uid
        $uid = model('User')->getUid($token);
        //获取课程里的课时展示的字段
        $field = implode($field, ',');
        //获取课时里展示教师的字段
        $teacher_field = implode($teacher_field, ',');
        //获取该课程下的学时信息
        $data['video'] = model('flv_movie')
            ->where('id', $id)
            ->field($field)
            ->find();

        //如果视频的movie_url为空 代表还没有录制完
        if (empty($data['video']->movie_url)) {
            result('40054');
        }

        //获取学习进度
        $jindu = model('user_plan')->where('uid', $uid)->where('mid', $id)->find();
        //如果没有学习进度 默认为0
        empty($jindu['progress']) ? $data['video']['progress'] = 0 : $data['video']['progress'] = $jindu['progress'];
        //如果没有学习记录默认为0
        empty($jindu['complete']) ? $jindu['complete'] = 0 : '';
        //视频是否全看完
        $data['video']['complete'] = $jindu['complete'];
        //执行查找该学时的教师信息
        $data['teacher'] = $this->getCourseTeacher($id, $teacher_field);
        //获取这个视频的弹题
        $question = model('testBase')
            ->field('content,answer,option,type')
            ->where('movie_id', $id)
            ->select();
        $china = ['一', '二', '三', '四', '五', '六', '七', '八', '九'];
        //处理数据
        for ($i = 0; $i < count($question); $i++) {
            //标题
            $question[$i]['title'] = '第' . $china[$i] . '题';
            //answer json => array
            $question[$i]['option'] = json_decode($question[$i]['option'], true);
            //答案处理成数组
            $answer = $question[$i]['answer'] . ',';
            $a = explode(',', $answer);
            unset($a[count($a) - 1]);
            //答案放进数组
            $question[$i]['answer'] = $a;
            if (!empty($question[$i]['option'])) {
                //option 只要value不要key
                $option = array_values($question[$i]['option']);
                $question[$i]['option'] = $option;
            } else {
                $question[$i]['option'] = '暂无选项';
            }
        }
        //弹题放进数组
        $data['question'] = $question;
        return $data;
    }

    /***
     * 通过课程id查找对应的课程包的教师信息
     * @param $id     课程id
     * @param $field  需要展示的教师字段
     * @return mixed  课程包教师信息
     */
    function getCourseTeacher($mid, $field = '')
    {
        $teacher_id = $this->hasOne('FlvMovie')->field('teacher_id')->where('id', $mid)->find()->teacher_id;

        //连教师表查询教师信息
        $data = model('TeaTeacher')
            ->field($field)
            ->where('id', $teacher_id)
            ->find();
        return $data;
    }

    /***
     * 返回课程名和课时数
     * @param $id
     * @return array
     */
    function shouKeChengMessage($id)
    {
        $data = $this
            ->field('title')
            ->where('id', $id)
            ->find()
            ->toArray();
        return $data;
    }

    /***
     * 获取这个课程的课程包id
     * @param $id 课程列表id
     * @return 课程包id  如果为空返回false
     */
    function getCourseId($id)
    {
        //通过课程查找课程包的id
        $id = $this->field('pid')->where('id', $id)->find();
        if (!empty($id)) {
            return $id->pid;
        } else {
            return false;
        }
    }

    /***
     * 获取视频所有评论
     * @param $mid   视频id
     * @return mixed
     */
    function getEvaluation($mid, $page, $count)
    {
        empty($count) ? $count = 10 : '';
        if (empty($mid)) result('40025');
        $data = model('Evaluation')
            ->alias('eva')
            ->join(config('database.prefix') . 'user_detail ud', 'eva.uid=ud.uid', 'left')
            ->field('avatar,name,content,eva.create_time,star')
            ->where('mid', $mid)
            ->order('eva.create_time desc')
            ->paginate($count, true, ['page' => $page]);
        $data = addPath($data, 'avatar', 'even');
        $data = json_decode(json_encode($data));

        return $data->data;
    }

    /***
     * 获取flvcategory 树型数据
     * @param $mid   视频id
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    function FlvCategoryGetTreeData()
    {
        $data = $this->getTreeData('tree', 'id', 'title');
        return $data;
    }

    /***
     * 插入flvcategory 数据
     * @param $data   数据集
     * @return $data
     * @data 2018/7/3 xuweiqi
     */
    function insertFlvCategory($request, $data)
    {
        $data = $this->save($data);
        return $data;
    }

    /***
     * 更新flvcategory 数据
     * @param $map   条件数据集
     * @param $info
     * @return $info
     * @data 2018/7/3 xuweiqi
     */
    function updataFlvCategory($map, $info)
    {
        $data = $this->where($map)->update($info);
        return $data;
    }

    /***
     * 查找flvcategory 数据
     * @param $id   传入的id
     * @data 2018/7/4 xuweiqi
     */
    function findFlvCategoryData($id)
    {
        $data = $this->where(['pid' => $id])->find();
        return $data;
    }

    /***
     * 删除flvcategory 数据
     * @param $id   传入的id
     * @data 2018/7/4 xuweiqi
     */
    function delFlvCategoryData($id)
    {
        $result = $this->where(['id' => $id])->delete();
        return $result;
    }

    /***
     * 查找flvcategory 状态值status
     * @param $map   查询条件
     * @result $data 数据集
     * @data 2018/7/4 xuweiqi
     */
    function findFlvCategoryStatus($map)
    {
        //先查出id对应的数据状态
        $status = $this->where($map)->field('status')->find();
        //如果0禁止 更新为1  如果1开启 更新为0
        if ($status['status'] == 0) {
            $info = array('status' => 1);
            $updateStatus = $this->updataFlvCategory($map, $info);
            return $updateStatus;
        } elseif ($status['status'] == 1) {
            $info = array('status' => 0);
            $updateStatusEmpty = $this->updataFlvCategory($map, $info);;
            return $updateStatusEmpty;
        } else {
            return false;
        }
    }


    public function getSignupList($where, $str = 0, $order = '')
    {
//        $this->table = empty($table)?$this->table:$table;
        $sel = ($str == 0) ? "select" : "find";
        $order = empty($order) ? "" : $order;
        return $this
            ->where($where)
            ->where("status=1")
            ->order($order)
            ->$sel();
    }


    public function getListJoin()
    {
//        dd($where);
        return $this
            ->alias("c")
            ->join("__TEA_TEACHER__ t", "c.teacher_id=t.id and t.status=1 and display=1", 'left')
            ->field("c.id,c.name,c.title,c.description,c.bag_img,c.bag_price,c.hours,c.pid,t.name as teacher_name,t.qg,t.referral,t.sex,t.phone")
            ->select();
    }

    public function getListForeach($arrCategoryList)
    {
        $parentArray = [];
        foreach ($arrCategoryList as $item) {
            if ($item['pid'] == 0) {
                array_push($parentArray, $item);
            }
        }
        foreach ($parentArray as $key => $parent) {
            $parentArray[$key]["childs"] = [];
            foreach ($arrCategoryList as $item) {
                if ($item['pid'] == $parent["id"]) {
                    $parentArray[$key]["childs"][] = $item;
                }
            }
            if ($parentArray[$key]["childs"] == []) {
                unset($parentArray[$key]);
            }
        }
        return $parentArray;
    }

    public function getData($name = null)
    {
        return parent::getData($name); // TODO: Change the autogenerated stub
    }

    /**
     * 获取企业购买课程名和img
     * @param id 课程包id string 1,2,3,4
     * @return mixed
     * user: liuxin
     */
    function getSelectCategory($id)
    {
        $data = $this
            ->field('id,name,title,bag_img')
            ->where('pid',0)
            ->where('id', 'in', $id)
            ->select();
        return $data;
    }

    function getAcademySelectCategory()
    {
        $data = $this
            ->field('id,name,title,bag_img')
            ->where('pid',0)
            ->select();
        return $data;
    }

    /***
     * 课程包下的课程和课时
     * @param $id
     * @return mixed
     */
    public function getListCategory($id,$trainId)
    {
        $data = $this
            ->field('id,name')
            ->where('pid', $id)
            ->select();
        foreach ($data as $k => $v) {
            $data[$k]['category_id'] = $id;
            $data[$k]['train_id'] = $trainId;
            $data[$k]['movie'] = model('FlvMovie')
                ->field('id,title,movie_url')
                ->where('category', $v['id'])
                ->select();
        }
        return $data;
    }

    /***
     * 获取所有课程包id,title
     * @return data
     */
    public function getCategoryData()
    {
        $data = $this
            ->field('id,name,title')
            ->where('pid=0')
            ->select();
        return $data;
    }


    public function categoryid($name)
    {
        $name = $this
            ->field('id')
            ->where('title',$name)
            ->find();
        if ($name) {
            return $name->id;
        }
        return false;
    }


}