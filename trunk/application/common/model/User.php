<?php

namespace app\common\model;

use app\api\controller\Pro;
use app\common\model\UserDetail as UserDetailModel;
use think\Db;
use think\Model;


/**
 * admin_user用户表模型
 */
class User extends Base
{
    //关闭自动写入时间
    protected $autoWriteTimestamp = false;

    /**
     * 注册用户
     * @param $data
     * @return array
     * @user 李海江 2018/7/13~上午10:46
     */
    function add($data)
    {
        //密码加密
        $token = create_token();
        $data['token'] = $token;
        $data['status'] = '1';
        //保存
        if ($this->save($data)) {
            //返回刚刚插入的id
            $arr = array(
                'last_id' => $this->id,
                'token' => $token,
            );
            return $arr;
        }
    }


    /**
     * 第三方登录
     * @param $data
     * @return array|bool
     * @user 李海江
     */
    function otherlogin($data)
    {
        if (empty($data['qqopenid']) && empty($data['wxopenid'])) {
            result('40027');
        }
        if (!empty($data['qqopenid'])) {
            $flag = 'qq';
        } else {
            $flag = 'wx';
        }
        $res = $this->hasOneCount($flag . 'openid', $data[$flag . 'openid']);
        //如果为空是第一次登陆
        if (empty($res)) {
            //需要进行绑定操作
            return false;
            //如果有值说明不是第一次登录
        } else {
            $token = create_token();
            if (empty($res['password'])) {
                $arr['nopwd'] = true;
            } else {
                $arr['nopwd'] = false;
            }
            $arr['token'] = $token;
            //获取id
            $id = $this->where($flag . 'openid', $data[$flag . 'openid'])->find()->id;
            //更新token
            $this->where('id', $id)->update(['token' => $token]);
            //获取用户头像, 姓名
            $brr = model('UserDetail')->where('uid', $id)->field('name,avatar')->find()->toArray();
            return array_merge($arr, $brr);
        }
    }

    /**
     * 查询是否有这个token
     * @param $token
     * @return int
     */
    function hasOneCount($field, $filed_value)
    {
        return $this->where($field, $filed_value)->find();
    }


    /**
     * 第三方绑定手机号
     * @param $data
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @user lihaijiang  2018/7/13 上午10:15
     */
    function bindLogin($data)
    {
        /***
         * 需要提交
         * openid
         * phone
         * code
         */
        if (empty($data['qqopenid']) && empty($data['wxopenid'])) {
            result('40027');
        }
        if (!empty($data['qqopenid'])) {
            $flag = 'qq';
        } else {
            $flag = 'wx';
        }
        //数据过滤
        $validate = Validate('User');
        if (!$validate->scene('bindphone')->check($data)) {
            //获取验证过滤错误码
            result((string)$validate->getError());
        }
        //验证验证码
        $res = check_code($data['code'], $data['phone']);
        //如果验证码不通过
        if (!$res) {
            result('40020');
        } else {
            $user = $this->where('phone', $data['phone'])->find();
            if (!empty($user)) {
                //如果有这个手机号
                $openid = $flag . 'openid';
                //如果这个手机号有openid
                if (!empty($user->$openid)) {
                    result('40031');
                    //如果这个手机号没有openid
                } else {
                    //创建一个token
                    $token = create_token();
                    $userdetail = model('UserDetail')->where('uid', $user->id)->find();
                    $brr = ['token' => $token, 'nopwd' => false, 'avatar' => $userdetail['avatar'], 'name' => $userdetail['name']];
                    //如果没有密码 给标识
                    if (empty($user->password)) {
                        $brr['nopwd'] = true;
                    }
                    $this->where('phone', $data['phone'])->update([$flag . 'openid' => $data[$flag . 'openid'], 'token' => $token]);

                    return $brr;

                }
            } else {

                //如果没有这个手机号要重新将openid和手机号 token新建一个
                $token = create_token();

                $data = array(
                    'phone' => $data['phone'],
                    $flag . 'openid' => $data[$flag . 'openid'],
                    'token' => $token,
                );
                if ($this->save($data)) {
                    //详情表里面查信息
                    model('UserDetail')->save(['uid' => $this->id, 'create_time' => strtotime('Y-m-:i:s')]);

                    Pro::$message['20016']['data'] = ['token' => $token, 'name' => '', 'avatar' => config('defaultavatar'), 'nopwd' => true];
                    result('20016');
                }

            }

        }
    }


    /**
     * 登录
     * @param $data
     * @param $header_token
     * @return array
     * @user lihaijiang  2018/7/13 上午10:17
     */
    function dologin($data, $header_token)
    {
        if (!empty($header_token)) {
            //验证token
            $res = $this->hasOneCount('token', $header_token);
            //如果没有这个token或者token不存在
            if (empty($res)) return ['flag' => false, 'id' => 3];
            //用户被冻结无法登陆
            if ($res->status == '-1') return ['flag' => false, 'id' => 5];
            //获取uid
            $uid = $this->getUid($header_token);
            //有这个token
            //返回 头像和姓名
            $basic = $this->getBasic($uid);
            return ['flag' => true, 'data' => $basic];
            //没有token的情况 就说明是第一次登录
        } else {
            //开启数据过滤
            $validata = Validate('User');
            //场景应用login
            if (!$validata->scene('login')->check($data)) {
                result($validata->getError());
            }
            //通过传递来的电话号查找用户
            $user = $this->hasOneCount('phone', $data['phone']);
            if (empty($user)) {
                //如果为空则用户不存在
                return ['flag' => false, 'id' => 1];
            } else {
                //用户被冻结无法登陆
                if ($user->status == '-1') return ['flag' => false, 'id' => 5];
                //没有token 账号也为冻结 正常登陆
                if ($data['password'] != $user['password']) {
                    //如果密码不正确登录失败
                    return ['flag' => false, 'id' => 2];
                } else {
                    /** 正常登陆 **/
                    //基础信息
                    $basic = $this->getBasic($user->id)->toArray();

                    $token = create_token();
                    //如果密码正确则登录
                    $arr = array(
                        'token' => $token,
                    );
                    //更新token
                    $this->where('phone', $data['phone'])->update($arr);
                    //返回信息
                    $arr = array_merge($basic, $arr);

                    return ['flag' => true, 'data' => $arr];
                }
            }
        }
    }


    /**
     * 获取用户id
     * @param $token
     * @return mixed
     * @user lihaijiang  2018/7/13 上午10:18
     */
    function getUid($token)
    {
        return $this->field('id')->where('token', $token)->find()['id'];
    }

    /**
     * 获取用户id
     * @param $token
     * @return mixed
     * @user lihaijiang  2018/7/13 上午10:18
     */
    function getUidByphone($phone)
    {
        return $this->field('id')->where('phone', $phone)->find()['id'];
    }

    /**
     * 获取用户类型 0企业 1院校
     * @param $token
     * @return mixed
     * @user 李海江 2018/9/4~上午10:12
     */
    function getType($token)
    {
        return $this->field('type')->where('token', $token)->find()['type'];
    }

    /***
     * @param string $uid 用户id
     * @return array 用户基础信息
     */
    function getBasic($uid)
    {
        $data = model('UserDetail')
            ->where('uid', $uid)
            ->field('avatar,name')
            ->find();
        return $data;
    }


    /**
     * 退出登录
     * @param $token
     * @return int
     * @user lihaijiang  2018/7/13 上午10:19
     */
    function doexit($token)
    {
        if ($this->where('token', $token)->update(['status' => '0', 'token' => ''])) {
            return 1;
        } else {
            return 0;
        }
    }

    /***
     * 修改密码
     * @param $data 用户数据
     * @return int 结果
     */
    function editUserPwd($data, $token)
    {
        $validata = Validate('User');
        //场景应用edit user password
        if (!$validata->scene('editUserPassword')->check($data)) {
            result($validata->getError());
        }
        //获取旧密码
        $user_data = $this->field('password')->where('token', $token)->find();
        //如果无通过token查找返回的用户信息 则用户不存在
        if (empty($user_data)) result('40002');
        //用户传递过来的密码
        $newpassword = $data['password'];
        //如果用户输入的旧密码与数据库一致 执行修改密码
        if ($newpassword == $user_data['password']) {
            //执行修改密码
            //修改新密码
            $this->where('token', $token)->update(['password' => $data['newpassword']]);
            result('20005');
        } else {
            result('40017');
        }
    }

    /***
     * 设置密码
     * @param $password  密码
     * @param $token     token
     * @return bool
     */
    function setPwd($data, $token)
    {
        $validata = Validate('User');
        //场景应用
        if (!$validata->scene('setpwd')->check($data)) {
            result($validata->getError());
        }
        $oldpassword = $this->where('token', $token)->find()->password;
        //如果秘密不为空不让设置
        if (!empty($oldpassword)) return false;
        //执行修改密码
        $res = $this->where('token', $token)->update(['password' => $data['password']]);
        if ($res) {
            return true;
        }
    }

    /**
     * 忘记密码
     */
    function forgetPassword($data)
    {

        //查找该用户是否存在
        $user_data = $this->where('phone', $data['phone'])->find();
        //如果无通过手机号查找返回的用户信息 则用户不存在
        if (empty($user_data)) result('40002');
        //执行修改密码
        $res = $this->where('phone', $data['phone'])->update(['password' => $data['password']/**,['token'=>$data['token']]**/]);
        return $res;
    }

    /***
     * 修改token
     * @param $phone 手机号
     * @param $token token
     */
    function saveToken($phone, $token)
    {
        //查找该用户是否存在
        $user_data = $this->where('phone', $phone)->find();
        //如果无通过手机号查找返回的用户信息 则用户不存在
        if (empty($user_data)) result('40002');
        $this->where('phone', $phone)->update(['token' => $token]);
    }

    /**
     * 保存头像
     * @param $url
     * @param $token
     * @return mixed
     */
    function saveAvatar($url, $token)
    {
        $uid = $this->getUid($token);
        return model('UserDetail')->where('uid', $uid)->update(['avatar' => $url]);
    }

    /**
     * 评价
     * @param $data
     * @param $token
     * @return false|int
     * @user 李海江
     */
    function evaluation($data, $token)
    {
        $uid = $this->getUid($token);
        $validate = Validate('User');
        if (!$validate->scene('evaluation')->check($data)) {
            result((string)$validate->getError());
        }
        $data['uid'] = $uid;
        $data['create_time'] = time();
        $res = model('evaluation')->save($data);
        return $res;
    }

    /***
     * 视频通知
     * @param $token
     */
    function getNotice()
    {
        $data = model('Message')->where('type', 2)->field('content')->select();
        return $data;
    }

    /***
     * 设置观看进度
     * @param $data
     * @param $token
     */
    function setProgress($data, $token)
    {
        $userplan = new UserPlan();
        $flvcategory = new FlvCategory();

        //uid
        $uid = $this->getUid($token);
        //需要传递进度
        if (empty($data['progress'])) result('40037');
        //视频id
        if (empty($data['mid'])) result('40025');
        $mid = $data['mid'];

        //提交该视频的课程包id
        $pid = $flvcategory
            ->alias('flv')
            ->join(config('database.prefix') . 'flv_movie mov', 'flv.id=mov.category', 'right')
            ->where('mov.id', $mid)
            ->find()->pid;


        //获取当前提交的视频在表里的个数
        $count = $userplan->where('mid', $mid)->where('uid', $uid)->count();
        //获取当前视频时长
        $hour = model('FlvMovie')->field('hour')->where('id', $data['mid'])->find()->hour;
        //如果当前播放时间大于视频总时长说明已经观看完毕
        $data['progress'] >= $hour ? $flag = true : $flag = false;
        //如果个数为0说明没有这个记录  新建
        if ($count == 0) {
            //准备入库信息
            $data = array(
                'mid' => $mid,
                'pid' => $pid,
                'uid' => $uid,
                'progress' => $data['progress'],
                'complete' => $flag,
                'create_time' => time(),
            );
            $res = $userplan->BaseSave($data);
        } else {
            $data = [
                'progress' => $data['progress'],
                'complete' => $flag,
                'update_time' => time(),
            ];
            $updateMap = ['uid' => $uid, 'mid' => $mid];
            //如果不等于0说明有这个记录 , 更新
            $res = $userplan->BaseUpdate($data, $updateMap);
        }

        if ($res) {
            $bag_count = $userplan->BaseFind(['uid' => $uid, 'pid' => 0, 'cid' => $pid], ['id']);

            $allcount = $flvcategory->getAllCourse('id', false, $pid)[0]['allcount'];
            $complete = $userplan->getBaifenBi($pid, $allcount, $uid);

            //准备视频包的入库信息
            $bagData = array(
//                'id' => $res+1,
                'complete' => intval($complete),
                'pid' => 0,
                'cid' => $pid,
                'uid' => $uid,
                'create_time' => time(),
                'update_time' => time(),
            );

            //SB TP5
            if ($bag_count['id']) {
                //Db::name('UserPlan')->update($bagData, ['id' => $bag_count['id']]);
                $userplan->update($bagData, ['id' => $bag_count['id']]);
            } else {
                //Db::name('UserPlan')->insert($bagData);
                $userplan->isUpdate(false)->data($bagData, true)->save();
//                $userplan->isUpdate(false)->save($bagData);
            }
            return $res;
        } else {
            result('40019');
        }


    }

    function createBagPlan()
    {

    }

    /***
     * 绑定第三方
     * @param $data
     * @param $token
     */
    function bindOpenid($data, $token)
    {
        if (empty($data['qqopenid']) && empty($data['wxopenid'])) {
            result('40027');
        }
        if (!empty($data['qqopenid'])) {
            $flag = 'qq';
        } else {
            $flag = 'wx';
        }
        $str = $flag . 'openid';
        $res = $this->hasOneCount($str, $data[$flag . 'openid']);


        //如果为空是第一次登陆
        if (empty($res)) {
            //获取uid
            $uid = $this->getUid($token);
            $openid = $this->where('id', $uid)->find()->$str;

            if (empty($openid)) {
                $array = array(
                    $flag . 'openid' => $data[$str],
                );
                $res = $this->where('id', $uid)->update($array);
                return $res;
            } else {
                result('40039');
            }
        } else {
            //如果有值说明这个微信绑定了其他账号

            //如果token 说明自己是自己
            if ($res['token'] == $token) {
                result('40047');
            } else {
                result('40038');
            }
        }
    }

    /***
     * 获取个人信息
     * @param $token
     * @param $field
     * @return mixed
     * @user 李海江
     */
    function getMy($token)
    {
        //获取自己当前是企业用户还是 院校用户
        $type = $this->ifSchoolUser($token);
        //获取自己的uid
        $uid = $this->getUid($token);

        if ($type) {//如果是院校
            //这里没有返回系名,app不想发版 未来要改
            $field = config('schoolMessage');
            $data = $this
                ->alias('u')
                ->join(config('database.prefix') . 'user_detail d', 'u.id=d.uid', 'left')
                ->join(config('database.prefix') . 'college_record cc', 'u.subjects_id=cc.id', 'left')
                ->field($field)
                ->where('u.id=' . $uid)
                ->find();
            //获取院校名
            $data['school_name'] = model('CollegeRecord')->BaseFind(['id' => $data['pid']])->academy_name;
            //删除pid
            unset($data['pid']);
        } else {//如果是企业
            $field = implode(config('my'), ',');
            $data = $this
                ->alias('u')
                ->join(config('database.prefix') . 'user_detail d', 'u.id=d.uid', 'left')
                ->join(config('database.prefix') . 'record qiye', 'd.enterprise_id=qiye.enterId', 'left')
                ->field($field)
                ->where('u.id=' . $uid)->find();
        }

        if (empty($data['avatar'])) $data['avatar'] = config('defaultavatar');
        return $data;
    }

    /**
     * 是否是院校用户
     * @param $token
     * @return mixed
     * @user 李海江 2018/9/13~下午3:16
     */
    function ifSchoolUser($token)
    {
        return $this->field('type')->where('token', $token)->find()['type'];
    }

    /**
     * 获取证书
     * @param $token
     * @return array
     * @user 李海江 2018/7/27~下午5:38
     */
    function getCertifiCate($token)
    {
        $exam = new Exam();
        $certificate = new Certificate();
        $Userdetail = new UserDetail();
        $flvcategory = new FlvCategory();

        $uid = $this->getUid($token);
        //查看自己的考试通过没通过
        $trainMap = ['uid' => $uid];
        $UserTrain = $exam->BaseSelect($trainMap, ['isqualified', 'cid']);


        for ($i = 0; $i < count($UserTrain); $i++) {
            //如果考试通过才可以打证
            if (isset($UserTrain[$i]['isqualified']) && $UserTrain[$i]['isqualified'] == 1) {
                //判断有无证书
                $cid = $UserTrain[$i]->cid;
                //查询证书数据表 查看当前课程是否有证书
                $certificate = $certificate->BaseFind(['uid' => $uid, 'cid' => $cid]);
                //如果有证书把url返回
                if (!empty($certificate)) {
                    $certificate_arr[] = array('url' => '/' . $certificate['url'], 'name' => $certificate['name']);
                } else {
                    $user_data = $Userdetail->BaseFind(['uid' => $uid]);
                    //如果没有证书调用生成证书的方法

                    //生成证书编号
                    $certificate_id = date('Ymd') . substr(md5(microtime(true)), 0, 6);
                    //选择获取证书时间
                    $date = date('Y年n月j日');


                    //二维码文件
                    $codeMessage = model('user')->getMy($token)->toArray();
                    $url_str = '';
                    foreach ($codeMessage as $k => $v) {
                        $url_str .= '&' . $k . '=' . $v;
                    }
                    //拼接二维码字符串
                    $url_str = urlencode(config('APP_NAME') . '/api/Code/?' . substr($url_str, 1));
                    //生成二维码
                    $ImgCode = 'http://qr.liantu.com/api.php?bg=ffffff&fg=000000&gc=0&el=l&w=115&m=3&text=' . $url_str;

                    //查找课程包下课程的名字
                    $kecheng_list_even_arr = $flvcategory->BaseSelect(['pid' => $cid], ['title']);

                    //转为二维数组
                    $kecheng_list_arr = array_merge_rec($kecheng_list_even_arr, 'title');
                    $kecheng_list = implode($kecheng_list_arr, '、');
                    //课程包的名字
                    $kechengbao_name = $flvcategory->BaseFind(['id' => $cid], ['title'])['title'];
                    $xueshi_count = $flvcategory->getAllCourse('id,title,bag_img', false, $cid)[0]['allcount'];

                    $id_sex = substr($user_data['idnumber'], 16, 1);
                    $sex = $id_sex % 2 == 0 ? '女' : '男';

                    $zhengshu_content = '完成' . $kechengbao_name . '培训，培训课程包扩' . $kecheng_list . '，共计' . $xueshi_count . '学时。';

                    //25个字是一行
                    $one_line = 25;

                    for ($j = 0; $j < mb_strlen($zhengshu_content); $j += $one_line) {
                        $zs_content[] = mb_substr($zhengshu_content, $j, $one_line);
                    }
                    $first_line = '       ____________(性别___身份证______________________)';
                    array_unshift($zs_content, $first_line);

                    //生成证书
                    $url = certificate($zs_content, config('linux_font'), $date, $certificate_id, $ImgCode, $user_data['name'], $sex, $user_data['idnumber']);
                    $mes = array(
                        'uid' => $uid,
                        'cid' => $cid,
                        'url' => $url,
                        'create_time' => time(),
                        'certificate_id' => $certificate_id,
                        'name' => $kechengbao_name,
                    );
                    //存进数据库
                    model('Certificate')->save($mes);
                    $certificate_arr[] = ['url' => '/' . $url, 'name' => $kechengbao_name];
                }
            }
        }

        if (!empty($certificate_arr)) {
            return $certificate_arr;
        } else {
            result('40043');
        }
    }


    /**
     * 获取企业学习进度
     * @param $token
     * @return string
     */
    function getLearnJd($token)
    {
        //获取用户类型 0企业 1院校
        $type = $this->getType($token);
        //获取用户id
        $uid = $this->getUid($token);

        if ($type === 0) {
            //查看我的企业购买了哪些课程
            //获取企业id
            $myqiye = $this->field('enterprise_id')->where('id', $uid)->find();
            //查找订单表里该企业支付猪状态为1的课程id
            if (!empty($myqiye->enterprise_id)) {
                $category = model('EnterpriseOrder')
                    ->field('category_id')
                    ->where('enterprise_id', $myqiye->enterprise_id)
                    ->where('order_status', '1')
                    ->select();

                if (!count($category) <= 0) {
                    foreach ($category as $v) {
                        $data[] = $v->category_id;
                    }
                } else {
                    return Pro::$message['40046'];
                }
            } else {
                result('40045');
            }
        } elseif ($type === 1) {
            $ownOrder = model('EnterpriseOrder')
                ->field('category_id')
                ->where('enterprise_id', $uid)
                ->where('type', '3')
                ->where('order_status', '1')
                ->select();
            if (!count($ownOrder) <= 0) {
                foreach ($ownOrder as $v) {
                    $data[] = $v->category_id;
                }
            } else {
                return 40056;
            }
        }
        //
        //以上就是获取我自己 或者 企业买了哪些课程包
        //


        $flvcategory = new FlvCategory();
        //获取我学习的课程包下共有多少课时
        for ($i = 0; $i < count($data); $i++) {
            //获得cid下面的课时共有多少 返回的是多维数组 把它转成二维数组
            $arr[$i] = $flvcategory->getAllCourse('id,title,bag_img', false, $data[$i]);
        }

        $userplan = new UserPlan();
        $exam = new Exam();
        if (!empty($arr)) {
            for ($i = 0; $i < count($arr); $i++) {
                $array[$i] = $arr[$i][0];

                $user_complete = $userplan->BaseFind(['pid' => 0, 'uid' => $uid, 'cid' => $array[$i]->id], ['complete', 'type']);
                if (empty($user_complete['complete']) && empty($user_complete['type'])) {
                    $array[$i]['percent'] = '0%';
                    $array[$i]['exam'] = 0;
                } else {
                    if (empty($user_complete['complete'])) {
                        $array[$i]['percent'] = '0%';
                    } else {
                        $array[$i]['percent'] = $user_complete['complete'] . '%';
                    }
                    //======考试不允许进行======
                    //$array[$i]['exam'] = 3;
                    //=======下======
                    //查看考试过没过 如果过了就不需要在考试
                    $is_exam = $exam->BaseFind(['cid' => $array[$i]->id, 'uid' => $uid]);
                    if (!empty($is_exam['isqualified']) && $is_exam['isqualified'] == 1) {
                        $array[$i]['exam'] = 2;
                    } else {
                        //如果学习进度大于100
                        if (intval($user_complete['complete']) >= 100) {
                            $array[$i]['exam'] = 1;
                        } else {
                            //如果学习进度没大于100要看看是不是集中培训
                            if ($user_complete['type'] == 1) {
                                $array[$i]['exam'] = 1;
                            } else {
                                $array[$i]['exam'] = 0;
                            }
                        }
                    }
                    //========上=======

                }
                //0. 不可以考试  1.可以考试 2.考试已通过
            }

            return $array;
        } else {
            return '';
        }
    }


    /**
     * 注册
     * @param $data
     */
    function register($data)
    {
        //数据过滤
        $validate = Validate('User');
        if (!$validate->scene('register')->check($data)) {
            //获取验证过滤错误码
            result($validate->getError());
        }
        //查询该手机号的记录
        $count = $this->hasOneCount('phone', $data['phone']);
        //如果大于1 已注册过
        if (!empty($count)) result('40012');
        //如果短信验证码与Cache里的不一致返回验证码错误
        $res = check_code($data['code'], $data['phone']);
        if (!$res) result('40020');
        /** 通过上一步 **/
        //获取需要插入数据库字段的对应数组
        $user = create_mysqlData($data, config('user'));
        //添加操作
        $arr = $this->add($user);
        //如果插入主表数据成功
        if ($arr['last_id'] > 0) {
            $user_detail = create_mysqlData($data, config('user_detail'));
            $user_detail['uid'] = $arr['last_id'];
            $user_detail['avatar'] = config('defaultavatar');
            //如果详情数据不为空
            if (!empty($user_detail)) {
                //执行插入详情表数据
                $res = model('UserDetail')->add($user_detail);
                if ($res) {
                    return $arr['token'];
                }
            }
        }
    }

    /**
     * 获取员工|学生数据 如果条件存在返回条件结果
     * @param array     condition 搜索条件
     * @param int       enterpriseid 数据id
     * @param int       type 角色类型
     * @return array    data
     * @user liuxin     2018/7/9
     **/
    public function getUsersData($condition, $id)
    {
        $userData = $this->join('__USER_DETAIL__ aud', 'aud.uid = hp_user.id');
        //搜索
        if (!empty($condition['name']) || !empty($condition['idnumber']) || !empty($condition['phone']) || !empty($condition['create_time'])) {
            $userData = $userData->where(function ($query) use ($condition) {
                $query->whereOr('aud.idnumber', trim($condition['idnumber']));
                if (!empty($condition['name'])) {
                    $query->whereOr('aud.name', 'like', '%' . trim($condition['name']) . '%');
                }
                if (!empty($condition['create_time'])) {
                    $query->whereOr('aud.create_time', 'like', '%' . trim($condition['create_time']) . '%');
                }
                $query->whereOr('hp_user.phone', trim($condition['phone']));
            });
        }
        return $userData
            ->where('hp_user.enterprise_id', $id)
            ->paginate(10);
    }

    /**
     * 用户总数
     * @param int       enterpriseid 企业id
     * return int       sum
     * @user liuxin     2018/7/9
     */
    public function getCount($id)
    {
        return $this->where('enterprise_id', $id)->count();
    }

    /**
     * 用户添加
     * @param array     user,user_detail 用户表，用户详情表数据
     * @return result
     * @user liuxin     2018/7/9
     */
    public function userCreate($user, $userDetail)
    {
        Db::startTrans(); //开启事务
        try {
            $userDetailModel = new UserDetailModel();
            if ($result = $this->create($user)) {
                $userDetail['uid'] = $result->id;
//                die;
                $detailResult = $userDetailModel->insert($userDetail);
                Db::commit(); //提交
                if ($detailResult) {
                    return '用户添加成功';
                }
            }
        } catch (\Exception $e) {
            Db::rollback(); //回滚
            throw $e;
        }
        return '用户添加失败';
    }

    public function selectPassword($id)
    {
        return $this
            ->field('password')
            ->select();
    }

    /**
     * 员工修改
     * @param array
     * @return result
     * @user liuxin     2018/7/9
     */
    public function userUpdate($user, $userDetail, $id)
    {
        Db::startTrans(); //开启事务
        try {
            $userDetailModel = new UserDetailModel();
            $result = $this->update($user);
            if ($result) {
                $uid['uid'] = $id;
                $detailResult = $userDetailModel->update($userDetail, $uid);
                Db::commit(); //提交
                if ($detailResult) {
                    return '用户修改成功';
                }
            }
        } catch (\Exception $e) {
            Db::rollback(); //回滚
            throw $e;
        }
        return '用户修改失败';
    }

    /**
     * 验证用户基本数据唯一，真实性
     * @param string     data 要验证的数据
     * @param string    field 要查询字段
     * @param int       userid 用户id
     * @return bool
     * @user liuxin     2018/7/9
     */
    public function onlyUser($data, $field, $userid = '')
    {
        if ($userid) {
            return $this->where($field, $data)
                ->where('id', 'neq', $userid)
                ->find();
        } else {
            return $this->where($field, $data)
                ->find();
        }

    }

    /**
     * 获取员工学习数据 如果条件存在返回条件结果
     * @param array     condition 搜索条件
     * @param int       enterpriseid 企业id
     * @param int       受集中培训的学员
     * @return array    data
     * @user liuxin     2018/7/9
     **/
    public function getStudyData($condition, $id, $trainid = false)
    {
        $searchResult = $this->join('__USER_DETAIL__ aud', 'aud.uid = hp_user.id');

        if (!empty($condition['name'])) {
            $searchResult = $searchResult->where('aud.name', 'like', '%' . $condition['name'] . '%');
        }
        if (!empty($condition['status'])) {
            $searchResult = $searchResult->where('hp_user.status', $condition['status']);
        }
        if (!empty($trainid)) {
            $searchResult = $searchResult->where('hp_user.id', 'in', $trainid);
        }
        $searchResult = $searchResult->where('hp_user.enterprise_id', '=', $id)->paginate(10, false, ['query' => request()->param()]);
        return $searchResult;
    }

    /**
     * admin获取员工学习数据 如果条件存在返回条件结果
     * @param array     condition 搜索条件
     * @param int       enterpriseid 企业id
     * @param int       受集中培训的学员
     * @return array    data
     * @user xuweiqi     2018/7/9
     **/
    public function adminGetStudyData($data, $id)
    {
        $searchResult = $this->alias("u")->join('__USER_DETAIL__ aud', 'aud.uid = u.id');
        $searchResult = $searchResult->where('aud.uid', 'in', $id)->where($data)->paginate();
        return $searchResult;
    }

    /**
     * admin集中培训获取员工学习数据 如果条件存在返回条件结果
     * @param array     condition 搜索条件
     * @param int       enterpriseid 企业id
     * @param int       受集中培训的学员
     * @return array    data
     * @user xuweiqi     2018/7/9
     **/
    public function adminXueGetStudyData($data, $id)
    {
        $searchResult = $this->alias("u")->join('__USER_DETAIL__ aud', 'aud.uid = u.id');
        if (!empty($data['id'])) {
            $searchResult = $searchResult->where('aud.uid', 'in', $id);
        }
        if (!empty($data['name'])) {
            $searchResult = $searchResult->where('aud.name', 'like', '%' . $data['name'] . '%');
        }
        $searchResult = $searchResult->paginate();
        return $searchResult;
    }


    /***
     * 查询订单信息
     * @param $id
     * @return array|false|null|\PDOStatement|string|Model
     */
    public function enterpriseOrder($id)
    {
        return model("EnterpriseOrder")->where('enterprise_id', $id)->where('order_status', 1)->find();
    }

    /***
     * admin查询订单信息
     * @param $id
     * @return array|false|null|\PDOStatement|string|Model
     */
    public function adminEnterpriseOrder()
    {
        return model("EnterpriseOrder")->where('order_status', 1)->paginate();
    }

    /**
     * 调用接口查询学习进度
     * @param object        学员数据
     * @return array        加入进度的学员数据
     * @user liuxin     2018/7/9
     */
    public function getStudyPlan($userData)
    {
        $userplanModel = new UserPlan();
        foreach ($userData as $k => $datum) {
            //查询集中培训状态
            $result = $userplanModel->getTrainPlan($userData[$k]['uid']);
            if ($result) {
                $userData[$k]['plan'] = '100%';
            } else {
                if ($userData[$k]['token']) {
                    $userData[$k]['plan'] = $this->getLearnJd($datum->token)[0]->percent;
                } else {
                    $userData[$k]['plan'] = '0%';
                }
            }

        }
        return $userData;
    }


    /***
     * 关联用户详情表
     * @return object
     * @user liuxin     2018/7/9
     */
    public function adminUserDetail()
    {
        return $this->hasOne('UserDetail', 'uid', 'id');
    }


    /***
     * 关联用户详情表
     * @return object
     * @user liuxin     2018/7/9
     */
    public function adminUserDetails()
    {
        return $this->hasMany('UserDetail', 'uid', 'id');
    }

    /***
     * 关联用户进度表
     * @return object
     * @user liuxin     2018/7/9
     */
    public function adminUserPlan()
    {
        return $this->hasOne('UserPlan', 'uid', 'uid');
    }

    public function getUserplanCount($id)
    {
        return $this
            ->field('id')
            ->where('enterprise_id', $id)
            ->count();
    }

    /***
     * 查询院校 系学生数量 学完学生数量
     * @param $id 系id
     * @param bool $type
     * @return mixed
     */
    public function subjectsShow($id, $type = false)
    {
        if ($type) {
            return $this
                ->field('id')
                ->join('__USER_PLAN__ aup', 'aup.uid = hp_user.id')
                ->where('subjects_id', $id)
                ->where('aup.complete', '100')
                ->count();
        } else {
            return $this
                ->field('id')
                ->where('subjects_id', $id)
                ->count();
        }
    }

    /***
     * 查询院校 学生数量 学完学生数量
     * @param $id 院校id
     * @param bool $type
     */
    public function collegeShow($id)
    {
        $userCount = $this
            ->field('id')
            ->where('enterprise_id', $id)
            ->count();

        $userplanCount = $this
            ->field('id')
            ->join('__USER_PLAN__ aup', 'aup.uid = hp_user.id')
            ->where('enterprise_id', $id)
            ->where('aup.complete', '100')
            ->count();
        return array(['userCount' => $userCount, 'userplanCount' => $userplanCount]);
    }


    /**
     * 获取个人信息是否完善的状态
     * @param $phone
     * @return bool
     * @user 李海江 2018/9/11~下午5:17
     */
    public function getOwnMessageState($phone)
    {
        $message = $this->BaseWithFind('adminUserDetail', ['phone' => $phone]);

        if (empty($message->enterprise_id) || empty($message->subjects_id) || empty($message->adminUserDetail->idnumber) || empty($message->adminUserDetail->name)) {
            return $array['error'] = true;
        } else {
            return $array['error'] = false;
        }
    }

    /***
     * 更新User表 subjuect_id为空
     * @param $where  条件
     * @param $save   修改数据
     */
    public function saveUserListByFirmNum($where, $save)
    {
        return $this
            ->where($where)
            ->update($save);
    }

    /***
     * 查询User表 subjuect_id存在
     * @param $where  条件
     */
    public function findUserListByFirmNum($where)
    {
        return $this
            ->where($where)
            ->find();
    }
}