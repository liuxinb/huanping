<?php
namespace app\common\model;
use app\common\model\CollegeRecord;

class Firmsign extends Base
{
    protected $autoWriteTimestamp = false;
    public function getList($where='',$field="*",$find='select')
    {
        return $this
            ->where($where)
            ->field($field)
            ->$find();
    }
    public function getJoinOne($where='',$field="*")
    {
        return $this
            ->alias('f')
            ->join("__RECORD__ r","f.id=r.enterId","left")
            ->where($where)
            ->field($field)
            ->find();
    }

    /***
     * 院校注册
     * @param $data
     */
    public function academyCrate($data)
    {
        $emailQuery= $this
                ->field('id')
                ->where('email',$data['email'])
                ->where('type',2)
                ->count();
        if ($emailQuery > 0) {
            return '手机号院校已注册，请直接登录';
        }
        $query = $this->create($data);
        return $query ? $query : false;
    }

    /***
     * 查询手机号注册情况
     * @param $phone
     * @param $type
     */
    public function phoneSelect($phone,$type)
    {
        $query = $this
                    ->field('id')
                    ->where('email',$phone)
                    ->where('type',$type)
                    ->count();
        if ($query <= 0) {
            return true;
        }
    }

    /***
     * @param $phone
     * @param $passwd
     */
    public function academySelect($phone,$passwd)
    {
        $CollegeModel = new CollegeRecord();
        $userinfo = $this
            ->field('*')
            ->where('email',$phone)
            ->where('password',$passwd)
            ->where('type',2)
            ->find();
        if ($userinfo) {
            //填写院校信息记录
            $info = $CollegeModel
                ->field('id')
                ->where('admin_id',$userinfo->id)
                ->count();
            if ($info > 0) {
                //填写
                $state = $CollegeModel
                    ->field('id')
                    ->where('state',1)
                    ->where('admin_id',$userinfo->id)
                    ->count();
                return $state<=0 ? -2: $userinfo;
            } else {
                //没填写
                \session('audit', $userinfo->id);
                return -4;
            }

        }
        return $userinfo ? $userinfo : -1;
    }

    /***
     * 查询院校数据
     * @param $id   传入的id
     * @data 2018/7/4 xuweiqi
     */
    public function selectAcademyData1($mapdata){
        $field='f.id,f.email,f.type,f.state,c.id,c.academy_name,c.create_time,c.pid';
        $map='f.type=2';
        $mapPid="c.pid=0";
        $mapData=$mapdata;
        $order='f.state=1';
        $data=$this::alias('f')
            ->join("__COLLEGE_RECORD__ c",'f.id=c.admin_id','left')
            ->where($map)
            ->where($mapData)
            ->where($mapPid)
            ->field($field)
            ->order($order)
            ->getTreeData('tree', 'c.id', 'c.academy_name')
            ->paginate();
        return $data;
    }


}