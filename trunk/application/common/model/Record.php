<?php
/**
 * Created by PhpStorm.
 * User: liuxin
 * date: 2018/7/9
 * Time: 下午2:35
 */
namespace app\common\model;

class Record extends Base
{
    /***
     *  查询企业提交档案记录
     * @param $id
     * @return int
     */
    public function recordFrequency($id)
    {
        return $this->where("enterId",$id)->count();
    }

    /***
     * 查询登录者信息 返回名称
     * @param $id
     * @retur string
     */
    public function recordInfo($id,$field='*')
    {
        return $this->field($field)
            ->where('enterId',$id)
            ->find();
    }

    /***
     * 添加档案
     * @param array     $data
     */
    public function recordCreate($data)
    {
        return $this->save($data);
    }

    protected function admin_user_detail()
    {
        return $this->hasOne('__USER_DETAIL__','enterprise_id','udid');
    }

    /***
     * 查询所有Record的数据
     * @param $map   条件数据集
     * @return $data
     * @data 2018/7/4 xuweiqi
     */
    function selectRecordData($map){
        $data=$this->where($map)->paginate();
        return $data;
    }

    /***
* 查询组合Record表数据
* param 查询数据
* return int
     *  @data 2018/7/4 xuweiqi
* */
    public function index_pub($dangan){
        $data=[];
        foreach ($dangan as $key => $v) {
            $data[$key]['id'] = $v->id? $v->id:'';
            $data[$key]['firmname'] = $v->firmname?$v->firmname:'';//档案表数据 企业名称
            $data[$key]['province'] = $v->province?$v->province:'';//档案表数据 省
            $data[$key]['city'] = $v->city? $v->city:'';//档案表数据 市
            $data[$key]['county'] = $v->county?$v->county:'';//档案表数据 县
            $data[$key]['addressphone'] = $v->addressphone?$v->addressphone:'';//档案表数据 办公地址
            $data[$key]['count'] = $this->staffCount($v->enterId)?$this->staffCount($v->enterId):'0';//员工总数
            $data[$key]['registersite'] = $v->registersite?$v->registersite:'';
            $data[$key]['invoicename'] = $v->invoicename?$v->invoicename:'';
            $data[$key]['identifynumber'] = $v->identifynumber?$v->identifynumber:'';
//            $data[$key]['openingnumber'] = $v->openingnumber?$v->openingnumber:'';
            $data[$key]['name'] = $v->name?$v->name:'';
            $data[$key]['phone'] = $v->phone?$v->phone:'';
            $data[$key]['email'] = $v->email?$v->email:'';
            $data[$key]['enterId'] = $v->enterId?$v->enterId:'';
        }
        return $data;
    }

     /***
     * 员工总数
     * @param 企业主键
     * @return int
     * @data 2018/7/4 xuweiqi
     * */
    public function staffCount($firmId)
    {
        $firmInt = model("UserDetail")->where(['enterprise_id'=> $firmId]);
        return $firmInt ? $firmInt->count() : 0;
    }

    /***
     * 更新 Record数据
     * @param $map 条件集合
     * @param $data 要更新的数据
     * @return data
     * @data 2018/7/4 xuweiqi
     * */
    public function updateRecordData($map,$data)
    {
        $request=$this->where($map)->update($data);
        return $request;
    }


}