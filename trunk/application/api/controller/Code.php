<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/6/19
 * Time: 上午11:58
 */

namespace app\api\controller;

use think\Controller;


/***
 * 扫描二维码
 * Class Code
 * @package app\api\controller
 */
class Code extends Controller
{
    /***
     * Android DownLoad
     */
    public function downloadApk()
    {
        //IOS
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            //如果是ios扫的安卓码 跳转到ios自己的方法里
            $this->redirect('downloadIos');
            //Android
        } else {
            //MicroMessenger 是android/iphone版微信所带的
            $ua = $_SERVER['HTTP_USER_AGENT'];
            //Windows Phone 是winphone版微信带的  (这个标识会误伤winphone普通浏览器的访问)
            if (strpos($ua, 'MicroMessenger') == false && strpos($ua, 'Windows Phone') == false) {
                $url = model('Version')->BaseFind(['type' => 'android'], ['url'], ['num' => 'desc'])->url;
                header("Location: " . $url);
                exit;
            } else {
                return view();
            }
        }
    }


    /***
     * ios暂无
     */
    public function downloadIos()
    {
        //MicroMessenger 是android/iphone版微信所带的
        $ua = $_SERVER['HTTP_USER_AGENT'];
        //Windows Phone 是winphone版微信带的  (这个标识会误伤winphone普通浏览器的访问)
        if (strpos($ua, 'MicroMessenger') == false && strpos($ua, 'Windows Phone') == false) {
            header("Location: https://itunes.apple.com/cn/app/id1402359741?mt=8");
            exit;
        } else {
            return view();
        }
    }


    /***
     * 证书扫描二维码
     */
    public function index()
    {
        $array = request()->get();
        $arr = ['0' => '女', '1' => '男'];
        $this->assign('arr', $arr);
        $this->assign('array', $array);
        return view();
    }
}