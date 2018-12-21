<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/5/31
 * Time: 下午6:04
 */
namespace app\common\model;

use think\Session;

class FlvMovie extends Base
{

    function getCover()
    {
        return $this->hasOne('FlvPicture', 'cover_id', 'id');
    }

    /***
     * 获取FlvMovie 的数据
     * @param $map 条件
     * @return data
     */
    function selectFlvMovieData($map)
    {
        $data = $this
            ->alias('m')
            ->join("__FLV_CATEGORY__ c", "m.category=c.id")
            ->join("__TEA_TEACHER__ t", "m.teacher_id=t.id", "left")
            ->field("m.id,m.title,m.actors,m.year,m.hour,m.update_time,m.display,t.name as teacher,m.hour,c.title as catet,m.movie_url,m.position")
            ->where($map)
            ->order("m.id desc")
            ->paginate();
        return $data;
    }

    /***
     * 查询组合FlvMovi表数据
     * param $data 数据集
     * return data
     *  @data 2018/7/4 xuweiqi
     * */
    public function groupFlvMoviePub($data){
        $datas = [];
        foreach ($data as $key => $item) {
            $tmp['id'] = $item->id;
            $tmp['title'] = $item->title;
            $tmp['catet'] = $item->catet;
            $tmp['actors'] = $item->actors;
            $tmp['year'] = $item->year;
            $tmp['name'] = $item->teacher;
            $tmp['movie_url'] = $item->movie_url;
            $tmp['hour'] = $item->hour+5;
            $tmp['update_time'] = $item->update_time;
            $tmp['display'] = $item->display;
            $tmp['position'] = $item->position;
            $datas[] = $tmp;

        }
        return $datas;
    }

    /***
     * 创建组合FlvMovie表数据
     * param $data 数据集
     * return data
     *  @data 2018/7/4 xuweiqi
     * */
    public function createFlvMovieDataPub($Movie,$hour){
        $movieDate = array(
            'title' => $Movie['title'],
            'category' => $Movie['category'],
            'actors' => SESSION::get('user')['username'],
            'path' => $Movie['path'],
            'content' => $Movie['content'],
            'teacher_id' => $Movie['teacher_id'],
            'hour' => $hour['hour'],
            'movie_url' =>$Movie['movie_url'],
            'year' => date("Y"),
            'update_time' => date("Y-m-d H:i:s"),
            'create_time' => date("Y-m-d H:i:s"),
        );
        return $movieDate;
    }

    /***
     * 创建FlvMovie表数据
     * param $movieDate 数据集
     * return data
     *  @data 2018/7/4 xuweiqi
     * */
    function createFlvMovieData($movieDate){
       $data= $this->create($movieDate);
        return $data;
    }
    /***
     * 查询FlvMovie表字段
     * param $movieDate 数据集
     * return data
     *  @data 2018/7/4 xuweiqi
     * */
    function selectFlvMovieField($map){
        $selData = FlvMovie::where($map)->field("cover_id")->select();
        return $selData;
    }

    /***
     * 删除FlvMovie表字段
     * param $movieDate 数据集
     * return data
     *  @data 2018/7/4 xuweiqi
     * */
    function deleteFlvMovieField($map){
        $request = $this->where($map)->delete();
        return $request;
    }

    /***
     * 查找FlvMovie表字段
     * @param $id 数据集
     * @return data
     *  @data 2018/7/4 xuweiqi
     * */
    function findFlvMovieField($id){
        $request =  $this->find($id);
        //修改视频获取时长 增加5秒
        $request['hour']=$request['hour']+5;
        return $request;
    }

    /***
     * 查找FlvMovie表字段
     * @param $map 数据集
     * @return data
     *  @data 2018/7/4 xuweiqi
     * */
    function valueFlvMovieFieldCoverId($map){
        $request = $this->where($map)->value('cover_id');
        return $request;
    }


    /***
     * 获取所有视频的id,title
     * @return $data
     *  @data 2018/7/4 xuweiqi
     * */
    public function movie_title_pub(){
        $movie_title= $this->field('id,title')->select();
//        $movie = array(
//            'movie_title' => $movie_title,
//        );
        return $movie_title;
    }

    /***
     * 获取所有视频的movie_url
     * @param $map 条件
     * @return $data
     *  @data 2018/7/4 xuweiqi
     * */
    public function getFlvMovieVidField($map){
        $data=$this->field('id,title,movie_url')->where($map)->find();
        return $data;
    }

    /**推荐试学视频 是否更改推荐
     * @param $dataMap 修改id
     * @param $data 修改内容
     * @return $this
     */
    public function dataUpdate($dataMap,$data)
    {
        return $this->where($dataMap)->update($data);
    }





}