<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/6/13
 * Time: 下午3:57
 */
namespace app\common\model;

use think\Session;

class FlvPicture extends Base
{

    /***
 * 创建FlvPicture数据
     * $param $pic
 * @return $data 添加的数据 path create_time
 * @data 2018/7/4 xuweiqi
 */
    function createFlvPictureData($pic){
        $result = $this->create($pic);
        return $result;
    }

    /***
     * 删除FlvPicture数据
     * @param $map_cover 条件
     * @return $data
     * @data 2018/7/4 xuweiqi
     */
    function deleteFlvPictureData($map_cover){
        $result = $this->where($map_cover)->delete();
        return $result;
    }

    /***
     * 查找FlvPicture字段
     * @param $infoPath 条件
     * @return $data
     * @data 2018/7/4 xuweiqi
     */
    function valueFlvPictureField($infoPath){
        $result = $this->where($infoPath)->value("path");
        return $result;
    }

    /***
     * 修改视频FlvPicture FlvMovie FlvMovieUrl字段
     * @param $resData FlvPicture要修改的数据 cover_id
     * @param $pic FlvPicture要修改的数据
     * @param $Movie 表单接收的数据
     * @param $map
     * @param $hour 时长
     * @return bool
     * @date 2018/7/5 xuweiqi
     */
    function updateFlvPicturePubData($Movie,$map,$hour){
                    $actor=  SESSION::get('user')['username'];
                    $actors['actors']=$actor;
                    $FlvMovieRes=model('FlvMovie')->where($map)->update(array_merge($Movie,$hour,$actors));
                    if ($FlvMovieRes === false) {
                        return false;
                    }else{
                        return true;
                    }

    }






}