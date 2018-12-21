<?php

namespace app\api\controller;

use think\Request;
use UploadImg\Img;

/**
 *  APP用户
 */
class User extends Pro
{
    /**
     * @var \think\Model
     */
    private $obj;

    /**
     * User constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->obj = model('User');
    }


    /***
     * 用户注册
     * @param Request $request
     */
    public function register(Request $request)
    {

        //暂未开放
        result('40057');

        $data = $request->post();
        $token = $this->obj->register($data);
        //返回
        Pro::$message['20001']['data'] = ['token' => $token, 'name' => '', 'avatar' => ''];
        result('20001');
    }

    /***
     * 第三方登录
     * @param Request $request
     */
    public function otherlogin(Request $request)
    {
        //获取app提交的数据
        $data = $request->post();
        //调用登录方法
        $res = $this->obj->otherlogin($data);
        if ($res) {
            //给头像添加路径
            $res = addPath($res, 'avatar', 'odd');
            //返回结果
            Pro::$message['20003']['data'] = $res;
            result('20003');
        } else {
            //暂时不允许 登录后 绑定手机号
            result('40058');
            //后续需要绑定手机号
            //result('40030');
        }
    }

    /***
     * 第三方登录绑定手机号
     * @param Request $request
     */
    public function bindLogin(Request $request)
    {
        //暂未开放
        result('40057');

        //获取app提交的手机号和code
        $data = $request->post();
        //执行绑定
        $data = $this->obj->bindLogin($data);
        $data = addPath($data, 'avatar', 'odd');
        Pro::$message['20016']['data'] = $data;
        result('20016');
    }

    /***
     * 登录
     * @param Request $request
     */
    public function login(Request $request)
    {
        //获取token  没有则为空
        $header_token = $request->header('token');
        //登录数据
        $data = $request->post();
        //执行登录操作
        $result = $this->obj->dologin($data, $header_token);
        if (!$result['flag']) {
            switch ($result['id']) {
                //用户不存在
                case 1:
                    $code = '40002';
                    break;
                //密码错误
                case 2:
                    $code = '40011';
                    break;
                //token失效
                case 3:
                    $code = '40009';
                    break;
                //该账号已被冻结
                case 5:
                    $code = '40018';
                    break;
            }

            result($code);
        } else {
            //给头像添加路径
            $result = addPath($result['data'], 'avatar', 'odd');
            Pro::$message['20003']['data'] = $result;
            result('20003');
        }
    }

    /***
     * 获取个人信息
     */
    public function getMy()
    {
        $token = request()->header('token');

        $data = $this->obj->getMy($token);
        //判断是否是院校的标识
        if (isset($data['school_name'])){
            $flag = true;
        }else{
            $flag = false;
        }

        $data = addPath($data, 'avatar', 'odd');
        Pro::$message['20020']['isSchool'] = $flag;
        Pro::$message['20020']['data'] = $data;
        result('20020');
    }

    /***
     * 退出登录
     * @param Request $request
     */
    public function doexit(Request $request)
    {
        $token = $request->header('token');
        $res = $this->obj->doexit($token);
        if ($res) {
            result('20004');
        } else {
            result('40013');
        }
    }


    /**
     * 修改用户信息
     * @param Request $request
     */
    public function modifyUserMessage(Request $request)
    {
        $data = $request->post();
        //数据验证
        $validata = Validate('User');
        //场景应用
        if (!$validata->scene('modifyUserMessage')->check($data)) {
            result($validata->getError());
        }
        //从主表获取用户uid
        $uid = $this->obj->getUid($request->header('token'));
        $data = create_mysqlData($data, config('user_detail'));
        //通过uid修改用户信息
        $result = model('UserDetail 
        7
       ')->modifyUserMessage($uid, $data);
        $result ? result('20002') : result('40019');
    }

    /***
     * 修改用户密码
     * @param Request $request
     */
    public function editUserPwd(Request $request)
    {
        //获取post的数据
        $data = $request->post();
        //用户修改自己的密码 需要传递旧密码
        $this->obj->editUserPwd($data, $request->header('token'));
    }

    /***
     * 设置密码
     * @param Request $request
     */
    public function setPwd(Request $request)
    {
        $token = $request->header('token');
        $data = $request->post();
        $res = $this->obj->setPwd($data, $token);
        if (!$res) {
            result('40032');
        } else {
            result('20017');
        }
    }

    /**
     * 忘记密码
     * @param Request $request
     */
    public function forgetPassword(Request $request = null)
    {
        $data = $request->post();
        $validata = Validate('User');
        //场景应用edit user password
        if (!$validata->scene('forgetPassword')->check($data)) {
            result($validata->getError());
        }
        //如果短信验证码与Cache里的不一致返回验证码错误 , 如果验证码错误直接return错误代码
        $res = check_code($data['code'], $data['phone']);
        //如果验证码不通过
        if (!$res) {
            result('40020');
        } else {
            //$token = create_token();
            //获取新的密码
            $data = array(
                'phone' => $data['phone'],
                'password' => $data['password'],
            );
            $this->obj->forgetPassword($data);
            result('20008');
        }
    }

    /***
     * 上传头像
     */
    public function uploadAvatar()
    {
        //获取token
        $token = request()->header('token');
        //上传图片类
        $info = Img::OneImg('avatar', config('upload.upload_avatar'));
        //如果返回的是对象说明上传没有问题
        if (is_object($info)) {
            $url = '/uploads' . config('upload.upload_avatar') . $info->getFilename();
            //保存数据
            $this->obj->saveAvatar($url, $token);
            //添加前缀
            $url = config('APP_NAME') . $url;
            Pro::$message['20015']['data'] = ['avatar' => $url];
            //返回结果
            result('20015');
        } else {
            //返回结果
            Pro::$message['40026']['message'] = $info;
            result('40026');
        }
    }

    /***
     * 获取学习进度
     */
    public function getLearnJd()
    {
        //获取token
        $token = request()->header('token');


        //获取学习进度
        $data = $this->obj->getLearnJd($token);

        if (!empty($data['code']) && $data['code'] == '40046') result('40046');

        //如果不为空返回
        if (!empty($data)) {
            if ($data == 40056){
                result('40056');
            }else {
                $data = addPath($data, 'bag_img');
                Pro::$message['20013']['data'] = $data;
                result('20013');
            }
        } else {
            //如果为空返回信息
            Pro::$message['40028']['message'] = '您还没有参加学习';
            result('40028');
        }
    }

    /***
     * 视频评价
     */
    public function evaluation()
    {
        $token = request()->header('token');
        //视频id 几星 内容
        $data = request()->post();
        $res = $this->obj->evaluation($data, $token);
        if ($res) {
            result('20019');
        }
    }

    /***
     * 获取通知
     */
    public function getNotice()
    {
        $data = $this->obj->getNotice();
        if (!empty($data)) {
            Pro::$message['20020']['data'] = $data;
            result('20020');
        } else {
            result('40036');
        }
    }

    /***
     * 设置视频观看长度
     */
    public function setProgress()
    {
        $token = request()->header('token');
        $data = request()->post();
        $res = $this->obj->setProgress($data, $token);
        if ($res) result('20015');
    }


    /***
     * 绑定第三方账号
     */
    public function bindOpenid()
    {
        $token = request()->header('token');
        $data = request()->post();
        $res = $this->obj->bindOpenid($data, $token);
        if ($res) result('20016');
    }

    /***
     * 获取证书
     */
    public function getCertifiCate()
    {
        $token = request()->header('token');
        $res = $this->obj->getCertifiCate($token);

        if ($res) {
            //添加url
            $arr = addPath($res, 'url');

            Pro::$message['20022']['data'] = $arr;
            result('20022');
        }

    }


}