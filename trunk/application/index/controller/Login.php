<?php

namespace app\index\controller;

use app\common\model\Firmsign;
use think\Controller;
use think\Request;
use \think\Validate;
use think\Session;
use app\common\model\EnterpriseOrder;
use app\common\model\Firmsign as FirmsignModel;

class login extends Controller
{
    protected $msg = [
        'email.require' => '手机号不能为空',
        'password.max' => '密码最多不能超过32个字符',
        'password.min' => '密码最少不能超过6个字符',
        'password.alphaDash' => '密码仅支持数字、字母',
        'ypassword.require' => '原始密码不能为空',
        'password.confirm' => '新密码和确认密码不一致'
    ];

    private $FirmsignModel;

    public function __construct()
    {
        $this->FirmsignModel = new FirmsignModel();
    }

    //企业注册
    public function register(Request $request)
    {
        //验证规则
        $validate = new Validate([
            'email' => 'require',
            'password' => 'require|alphaDash|max:32|min:6',
        ], $this->msg);

        $data = $request->param();

        if (!$validate->check($data)) {
            echo $validate->getError();
            die;
        }
        if (!preg_match('/^[1][3,4,5,6,7,8,9][0-9]{9}$/', $data['email'])) {
            echo '手机号码格式错误,请重新输入！';
            die;
        }
        //验证码
        $code = $data['yzm'];
        if ($code != \think\Cache::get($data['email']) || $code == null) {
            echo '短信验证码错误,请重新输入！';
            die;
        } else {
            //当验证码验证通过后从缓存中清除code
            \think\Cache::rm($data['email']);
        }
        $phonedata = Firmsign::where(['email' => $data['email']])->find();

        if ($phonedata) {
            return $phonedata->type == 1 ? -78 : -79;
        }

        //处理数据
        $data = [
            'email' => $request->param('email'),
            'password' => md5($request->param('password')),
            'type' => 1
        ];
        $userinfo = Firmsign::create($data);
        //响应
        if ($userinfo->id) {
            Session::set('rootId', $userinfo->id);
            session('adminRole', $userinfo);
            return '申请成功，正在为你跳转~';
        } else {
            return '';
        }
    }

    //院校注册（需审核）
    public function academyRegister(Request $request)
    {
        $data = $request->param();
        //数据验证
        $validate = new Validate([
            'email' => 'require',
            'password' => 'require|alphaDash|max:32|min:6',
        ], $this->msg);
        if (!$validate->check($data)) {
            echo $validate->getError();
            die;
        }
        if (!preg_match('/^[1][3,4,5,6,7,8,9][0-9]{9}$/', $data['email'])) {
            echo '手机号码格式错误,请重新输入！';
            die;
        }
        //验证码
        if ($data['yzm'] != \think\Cache::get($data['email']) || $data['yzm'] == null) {
            echo '短信验证码错误,请重新输入！';
            die;
        } else {
            //当验证码验证通过后从缓存中清除code
            \think\Cache::rm($data['email']);
        }

        $phonedata = Firmsign::where(['email' => $data['email']])->find();

        if ($phonedata) {
            return $phonedata->type == 1 ? -78 : -79;
        }

        $phonedata = Firmsign::where(['email' => $data['email']])->count();
        if ($phonedata > 0) {
            return -28;
        }
        //拼接
        $dataArray = [
            'email' => $data['email'],
            'password' => md5($data['password']),
            'type' => 2,
        ];
        $query = $this->FirmsignModel->academyCrate($dataArray);
        if ($query) {
            Session::set('audit', $query->id);
            return -17;
        } else {
            return '申请失败';
        }
    }

    /**
     * 个人注册
     * @param Request $request
     * @return mixed
     * @user 李海江 2018/9/3~上午10:42
     */
    public function ownRegister(Request $request)
    {
        $data = $request->post();

        //拼接
        $dataArray = [
            'phone' => $data['phone'],
            'password' => md5($data['password']),
            'type' => 1,
            'code' => $data['yzm'],
        ];

        $res = model('user')->register($dataArray);

        if (is_string($res)) {
            //存session
            $user = array(
                'phone' => $data['phone'],
                'type' => 3
            );
            Session::set('user', $user);
            return ['success' => true, 'message' => '注册成功'];
        } else {
            return $res;
        }
    }

    public function loginOut()
    {
        \session('rootId', NULL);
        \session('adminRole', NULL);
        setcookie('rootId', '', time() - 3600 * 24, '/');


        return "<script>parent.location.href='/'; </script>";
    }

    public function login(Request $request)
    {
        $data = $request->param();
        //验证码
        if (!captcha_check($data['yzm'])) {
            return -3;
        }
        //个人登录
        if ($data['type'] == 3) {
            $ownData = array(
                'phone' => $data['email'],
                'password' => md5($data['password']),
            );

            //通过传递来的电话号查找用户
            $user = model('user')->hasOneCount('phone', $ownData['phone']);
            if (empty($user)) {
                //如果为空则用户不存在
                return -6;
            } else {
                if ($user->type == 0) {
                    return -77;
                } else {
                    //用户被冻结无法登陆
                    if ($user->status == '-1') return -10;
                    if ($ownData['password'] != $user['password']) {
                        //如果密码不正确登录失败
                        return -2;
                    } else {

                        $user = array(
                            'phone' => $ownData['phone'],
                            'type' => 3
                        );
                        Session::set('user', $user);
                        return -110;
                    }
                }
            }
        }
        //查询账号有效性
        $phoneSelect = $this->FirmsignModel->phoneSelect($data['email'], $data['type']);
        if ($phoneSelect) {
            return -6;
        }
        //登录类型
        if ($data['type'] == 1) {
            $rootEmail = $data['email'];
            $rootPasswd = $data['password'];
            $where['f.email'] = $rootEmail;
            $where['f.password'] = md5($rootPasswd);
            $objModel = new FirmsignModel();
            $result = $objModel->getJoinOne($where, "f.*,r.firmname");
            if ($result) {
                \session('rootId', $result->id);
                session('adminRole', $result);
                if ($result->firmname == '') {
                    \session('rootName', $result->email);
                } else {
                    \session('rootName', $result->firmname);
                }
                $rootCheck = $request->only('check')['check'];
                if (!empty($rootCheck)) {
                    setcookie('rootId', $result->id, time() + 3600 * 24, '/');

                    if ($result->firmname == '') {
                        setcookie('rootName', $result->email, time() + 3600 * 24, '/');
                    } else {
                        setcookie('rootName', $result->firmname, time() + 3600 * 24, '/');
                    }
                }
                $objSignup = new EnterpriseOrder();
                $order['order_status'] = 'desc';
                $type = \session('adminRole')->type;
                $arrSignupList = $objSignup->getSignupList(array('enterprise_id' => \session('rootId'), 'type' => $type), 1, $order);
                $count = !empty($arrSignupList) ? count($arrSignupList) : 0;
                return $count > 0 ? ($arrSignupList['order_status'] > 1 ? $arrSignupList['category_id'] : 0) : -1;
            }
            return -2;
        } else if ($data['type'] == 2) {
            //查询院校账号信息
            $userinfo = $this->FirmsignModel->academySelect($data['email'], md5($data['password']));

            if ($userinfo == -1) {
                return -2; //账号密码错误
            } else if ($userinfo == -2) {
                return -18;
            } else if ($userinfo == -4) {
                return -16;
            }
            //存到session
            \session('rootId', $userinfo->id);
            session('adminRole', $userinfo);
            //跳转到院校首页
            return -1;
        }
    }
}