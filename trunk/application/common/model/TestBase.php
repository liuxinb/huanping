<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/6/11
 * Time: 下午5:47
 */
namespace app\common\model;

class TestBase extends Base
{
    /***
     * 查询TestBase单选题全部数据
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function selectTestBaseBinaryData($map)
    {
        $dataBinary=$this->alias('b')
            ->join("__FLV_MOVIE__ m","b.movie_id=m.id","left")
            ->field("b.id,b.content,b.answer,b.type,b.A,b.B,b.movie_id,m.title,b.create_time")
            ->where($map)->order('b.create_time desc')->paginate();
        return $dataBinary;
    }

    /***
 * 查询Authgroup全部数据
 * @return data
 * @data 2018/7/5 xuweiqi
 */
    function selectTestBaseSingleMultipleData($map)
    {
        $dataSingleMultiple=TestBase::alias("b")
            ->join("__FLV_MOVIE__ m","b.movie_id=m.id","left")
            ->field("b.id,b.content,b.answer,b.A,b.type,b.B,b.C,b.D,b.movie_id,m.title,b.create_time")
            ->where($map)->order('b.create_time desc')->paginate();
//        $single=$this->pub($dataSingle);
      return $dataSingleMultiple;
    }

    /***
     * 查询Authgroup全部数据
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function  selectTestBaseSingleMultipleDataPub($dataSingle)
    {
        $datas = [];
        foreach ($dataSingle as $key => $item) {
            $tmp['id'] = $item->id;
            $tmp['content'] = $item->content;
            $tmp['A'] = $item->A?$item->A:'';
            $tmp['B'] = $item->B?$item->B:'';
            $tmp['C'] = $item->C?$item->C:'';
            $tmp['D'] = $item->D?$item->D:'';
            $tmp['answer'] = $this->answer_pub($item->answer);
            $tmp['title'] = $item->title?$item->title:'' ;
            $tmp['create_time'] = $item->create_time ;
            $tmp['movie_id'] = $item->movie_id ?$item->movie_id:'' ;
            $datas[] = $tmp;
        }
        return $datas;
    }

    //    答案0,1,2,3代替A,B,C,D strpos=$v.answer,"0"?str_replace="0","A",
    public function answer_pub($data){
        if(!(strpos($data, '0')==-1)){
            $datas=  str_replace('0', 'A', $data);
            if(!(strpos($datas,'1')==-1)){
                $datas=  str_replace('1', 'B', $datas);
                if (!(strpos($data, '2')==-1)){
                    $datas=  str_replace('2', 'C', $datas);
                    if(!(strpos($data, '3')==-1)){
                        $datas=  str_replace('3', 'D', $datas);
                    }
                }
            }

        }
        return $datas;
    }

    /***
     * 修改数据
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function updateTestBaseData($map,$data)
    {
        $res=$this->where($map)->update($data);
        return $res;
    }

    //        //修改答案接收答案公用方法
    public function editPub(){
        $datas=input('post.');
//        dump($datas);die;
        if(isset($datas['answer'])){
            if(($datas['answer'][0])=='0'){
                $data['answer']='0';
            }elseif(($datas['answer'][0])=='1'){
                $data['answer']='1';
            }
            $data['create_time']=time();
            $data['content']=$datas['content'];
//            if(empty($datas['movie_id'])){
//                unset($data['movie_id']);
//            }else{
                $data['movie_id']=  $datas['movie_id'];
//            }
            return $data;
        }else{
            $this->error('未选择选项');
        }
    }

    /***
     * 查询TestBase全部数据
     * @param $map 条件 id
     * @return $data
     * @data 2018/7/5 xuweiqi
     */
    function deleteTestBaseData($map)
    {
        $data= $this->where($map)->delete();
        return $data;
    }

    /***
     * 添加判断题TestBase处理数据
     * @param $map 条件 id
     * @return $data
     * @data 2018/7/5 xuweiqi
     */
    function addBinaryTestBaseData()
    {
        $data=$this->editPub();
        $data['type']=3;
        $data['create_time']=time();
        $ress=[
            'A'=>'正确',
            'B'=>'错误',
        ];
        $data['A']= '正确';
        $data['B']= '错误';
        $data['option']=  json_encode($ress);

        $res=$this->insert($data);
        return $res;
    }

    /***
     * 获取单选题数据并TestBase处理数据
     * @param $map 条件 id
     * @return $data
     * @data 2018/7/5 xuweiqi
     */
    function getSingleData()
    {
        $data=input('post.');
        $ress=[
            'A'=>$data['A'],
            'B'=>$data['B'],
            'C'=>$data['C'],
            'D'=>$data['D'],
        ];
        $data['answer']=$data['answer'][0];
        $data['create_time']=time();
        $data['option']=  json_encode($ress);
        $data['movie_id']=  $data['movie_id'];
        return $data;
    }


    /***
     * 修改单选题,多选题数据
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function updateSingleTestBaseData($data)
    {
        $res=$this->update($data);
        return $res;
    }

    /***
     * 添加单选题数据
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function addSingleData()
    {
        $data=input('post.');
        $ress=[
            'A'=>$data['A'],
            'B'=>$data['B'],
            'C'=>$data['C'],
            'D'=>$data['D'],
        ];
        $data['answer']=$data['answer'][0];
        $data['type']=1;
        $data['create_time']=time();
        $data['option']=  json_encode($ress);
        $data['movie_id']=  $data['movie_id'];
        $res=$this->insert($data);
        return $res;
    }
    /***
     * 获取表单数据 修改多选题数据
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function editMultData()
    {
         $data=input('post.');
          $ress=[
            'A'=>$data['A'],
            'B'=>$data['B'],
            'C'=>$data['C'],
            'D'=>$data['D'],
        ];
        $data['create_time']=time();
        $data['option']=  json_encode($ress);
        $data['movie_id']=  $data['movie_id'];
        return $data;
    }

    /***
 * 获取表单数据 处理多选题条件
 * @return data
 * @data 2018/7/5 xuweiqi
 */
    function addMultData()
    {
        $data=input('post.');
        $ress=[
            'A'=>$data['A'],
            'B'=>$data['B'],
            'C'=>$data['C'],
            'D'=>$data['D'],
        ];
        $data['type']=2;
        $data['create_time']=time();
        $data['option']=  json_encode($ress);
        $data['movie_id']=  $data['movie_id'];
        return $data;
    }

    /***
     * 插入多选题条件
     * @return data
     * @data 2018/7/5 xuweiqi
     */
    function insertAddMultData($data)
    {
        $data=$this->insert($data);
        return $data;
    }


    //    答案A,B,C,D代替0,1,2,3 strpos=$v.answer,"0"?str_replace="0","A",
    public function answerPubData($data){
        if(!(strpos($data, 'A')==-1)){
            $datas=  str_replace('A', '0', $data);
            if(!(strpos($datas,'B')==-1)){
                $datas=  str_replace('B', '1', $datas);
                if (!(strpos($data, 'C')==-1)){
                    $datas=  str_replace('C', '2', $datas);
                    if(!(strpos($data, 'D')==-1)){
                        $datas=  str_replace('D', '3', $datas);
                    }
                }
            }

        }
        return $datas;
    }




}