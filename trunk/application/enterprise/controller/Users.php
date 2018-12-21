<?php

namespace app\enterprise\controller;

use app\common\model\User;
use app\common\model\UserDetail;
use app\common\controller\EnterBase;
use app\common\model\CollegeRecord;
use think\Request;
use \think\Validate;

class Users extends EnterBase
{
    private $userModel;
    private $userdetailModel;
    private $adminRole;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->userModel = new User();
        $this->userdetailModel = new UserDetail();
        $this->adminRole = parent::role();
        $this->CollegeModel = new CollegeRecord();
    }

    const freeze = '-1';
    const unfreeze = '1';

    protected $msg = [
        'name.require' => '姓名不能为空',
        'password.require' => '密码不能为空',
        'idnumber.require' => '身份证号不能为空',
        'idnumber' => '身份证格式错误',
        'phone.require' => '手机号不能为空',
        'phone' => '手机号格式不正确',
        'in_time.require' => '入职时期不能为空',
        'in_time' => '入职时期格式错误',
    ];

    protected $validate = [
        'name' => 'require',
        'phone' => ['/^1[3456789]\d{9}$/', 'require'],
        'password' => 'require',
        'idnumber' => ['/(^\d(15)$)|((^\d{18}$))|(^\d{17}(\d|X|x)$)/', 'require'],
        'in_time' => ['/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/']
    ];

    /***
     *  展示企业员工 or 院校学员数据
     * @param Request $request
     * @return \think\response\View
     */
    public function index(Request $request)
    {
        //获取ID
        $enterpriseId = session('rootId');
        //请求
        $userRequest = $request->except(['page', 'result']);
        //获取数据
        $searchResult = $this->userModel->getUsersData($userRequest, $enterpriseId);
        //分页
        $pageResult = $searchResult->render();
        //总数
        $total = $this->userModel->getCount($enterpriseId);
        //返回
        return view('users/index',
            [
                'data' => $searchResult,
                'page' => $pageResult,
                'total' => $total,
                'adminRole' => $this->adminRole
            ]);
    }

    /***
     * 添加员工数据
     * @param Request $request
     * @return \app\common\model\
     */
    public function add(Request $request)
    {
        //院校
        if ($this->adminRole == 2) {
            echo '无权操作';
            die;
        }
        //获取ID
        $enterpriseId = session('rootId');
        //请求
        $data = $request->param();
        //验证数据合法性
        $validate = new Validate($this->validate, $this->msg);
        if (!$validate->check($data)) {
            echo $validate->getError();
            die;
        }
        //验证手机号唯一性
        $onlyphone = $this->userModel->onlyUser($data['phone'], 'phone');
        if ($onlyphone) {
            echo '手机号已存在~';
            die;
        }
        //验证身份证真实性
        if (!validation_filter_id_card($data['idnumber'])) {
            echo '身份证不合法~';
            die;
        }
        //验证身份证唯一性
        $onlyphone = $this->userdetailModel->onlyUserdetail($data['idnumber'], 'idnumber');
        if ($onlyphone) {
            echo '身份证号已存在~';
            die;
        }
        //处理数据
        $users = [
            'phone' => $data['phone'],
            'password' => md5($data['password']),
            'in_time'=>$date = $data['in_time'],
            'update_time' => date('Y-m-d H:i:s'),
            'enterprise_id' => $enterpriseId,
            'type' => 0,
        ];
        $userDetails = [
            'name' => $data['name'],
            'idnumber' => $data['idnumber'],
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
            'enterprise_id' => $enterpriseId,
        ];
        //数据添加操作
        $userResult = $this->userModel->userCreate($users, $userDetails);
        //响应
        return $userResult;
    }

    /***
     * 员工状态
     * @param $id
     * @param $status
     * @return string
     */
    public function unfreezeStatus($id, $status)
    {
        $staffmessage = $this->userModel->find($id);
        $status == self::unfreeze ? $staffmessage->status = self::unfreeze : $staffmessage->status = self::freeze;
        $result = $staffmessage->save();
        if ($result) {
            return '操作成功';
        } else {
            return '操作频繁，稍后再试';
        }
    }

    /**
     * 返回要修改的数据
     * @param int $id
     * @param int $pageNumber
     * @return json
     */
    public function update($id)
    {
        $data = $this->userModel->find($id);
        $data->name = $data->adminUserDetail->name;
        $data->create_time = date('Y-m-d', strtotime($data->adminUserDetail->create_time));
        $data->idnumber = $data->adminUserDetail->idnumber;
        unset($data->password);
        return json($data);
    }

    /**
     * 保存员工数据
     * @param array $request
     * @return mixed     data
     * */
    public function save(Request $request)
    {
        //获取ID
        $enterpriseId = session('rootId');
        //请求
        $data = $request->param();
        $data['password'] = empty($data['password']) ? false : $data['password'];
        //验证数据合法性
        $validate = new Validate($this->validate, $this->msg);
        if (!$validate->check($data)) {
            echo $validate->getError();
            die;
        }
        //验证手机号唯一性
        $userid = $request->only('id')['id'];
        $onlyphone = $this->userModel->onlyUser($data['phone'], 'phone', $userid);
        if ($onlyphone) {
            echo '手机号已存在~';
            die;
        }
        //验证身份证真实性
        if (!validation_filter_id_card($data['idnumber'])) {
            echo '身份证不合法~';
            die;
        }
        //验证身份证唯一性
        $onlyphone = $this->userdetailModel->onlyUserdetail($data['idnumber'], 'idnumber', $userid);
        if ($onlyphone) {
            echo '身份证号已存在~';
            die;
        }
        //处理数据
        if ($data['password']) {
            $users = [
                'id' => $data['id'],
                'phone' => $data['phone'],
                'password' => md5($data['password']),
                'enterprise_id' => $enterpriseId,
            ];
        } else {
            $users = [
                'id' => $data['id'],
                'phone' => $data['phone'],
                'enterprise_id' => $enterpriseId,
            ];
        }
        $users['update_time'] = date('Y-m-d H:i:s');
        if ($this->adminRole == 1) {
            $users['in_time'] = $data['in_time'];
        }
        $userDetails = [
            'name' => $data['name'],
            'idnumber' => $data['idnumber'],
            'update_time' => date('Y-m-d H:i:s'),
            'enterprise_id' => $enterpriseId,
        ];
        $uid = $request->only('id')['id'];
        //数据保存操作
        $userResult = $this->userModel->userUpdate($users, $userDetails, $uid);
        //响应
        return $userResult;
    }

    /**
     * 批量导入视图
     * @return \think\response\View
     */
    public function batchIndex()
    {
        if ($this->adminRole == 2) {
            return $this->redirect('/qiyes', '无权访问');
        }
        return view('users/batch');
    }

    //院校
    public function academy()
    {
        return view('users/academy');
    }

    public function academyBatch(Request $request)
    {
        //获取ID
        $enterpriseId = session('rootId');
        //判断文件夹是否存在不存在则创建
        if (!file_exists(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId)) {
            mkdir(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId, 0777, true);
        }
        //处理Excel文件请求
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $file = request()->file('file');
        if (is_null($file)) {
            return $this->success("上传文件不能为空~", '/enterprise/users/academy');
        }
        $info = $file->validate(['size' => 512000, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId);
        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId . DS . $exclePath;   //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
            echo "<pre>";
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            array_shift($excel_array);  //删除第一个数组(标题);
            //院校：姓名 身份证号 手机号 默认密码 企业id 类型type 入学时间  subjects_id
            $sumCount = 0;
            $data = [];
            foreach ($excel_array as $k => $v) {
                $data[$k]['name'] = trim($v[0]); //学生姓名
                $data[$k]['idnumber'] = trim($v[1]); //身份证
                $data[$k]['phone'] = trim($v[2]); //手机号
                $data[$k]['password'] = trim($v[3]); //密码
                $data[$k]['subjects_id'] = trim($v[4]);//院系
                $data[$k]['create_time'] = trim(date('Y-m-d '));//入学时间
                $data[$k]['type'] = '1';//类型
                $sumCount++;
            }

            //删除空数据
            foreach ($data as $k => $v) {
                if (preg_match("/\s/", $v['name'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['idnumber'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['phone'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['password'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['subjects_id'])) {
                    unset($data[$k]);
                }
            }
            //验证导入的数据真实性
            $newArray = $this->academyverificationData($data);
            if (!$newArray) {
                return $this->success("请检查数据有效性,重新上传~", '/enterprise/users/academy');
            }

            //程序给转换院系
            $q = 1;
            foreach ($newArray as $k => $v) {
                $q++;
                $subjectId = $this->CollegeModel->subjectsid($v['subjects_id'], $enterpriseId);
//                dd($subjectId);
                if (!$subjectId) {
                    return $this->success('导入失败:第' . $q . '行，院系不存在', '/enterprise/users/academy');
                } else {
                    $newArray[$k]['subjects_id'] = $subjectId;
                }
            }
            //拼接成两个数组，分别插入数据
            $user = [];
            $user_detail = [];
            $i = 0;
            foreach ($newArray as $k => $v) {
                $user_detail[$k]['name'] = $v['name']; //姓名
                $user_detail[$k]['idnumber'] = $v['idnumber']; //身份证
                $user[$k]['phone'] = $v['phone']; //手机号
                $user[$k]['password'] = md5($v['password']); //密码
                $user[$k]['type'] = $v['type']; //院校
                $user[$k]['subjects_id'] = $v['subjects_id']; //院系
                $user_detail[$k]['create_time'] = date('Y-m-d '); //导入时间
                $i++;
            }
            $phone = array_column($user, 'phone');
            $idnumber = array_column($user_detail, 'idnumber');

            $repeat_arr = repeat_arr($phone);
            $repeat_num = implode(array_values($repeat_arr), '，');

            $repeat_idnumarr = repeat_arr($idnumber);
            $repeat_idnum = implode(array_values($repeat_idnumarr), '，');

            if (!empty($repeat_arr)) {
                return $this->success('导入失败: 表格中' . $repeat_num . '手机号重复', '/enterprise/users/academy');
            };

            //验证表格身份证号唯一性
            if (!empty($repeat_idnum)) {
                return $this->success('导入失败: 表格中存在重复身份证号，为：' . $repeat_idnum, '/enterprise/users/academy');
            }
            //数据添加操作
            $s = 0;
            foreach ($user as $key => $item) {
                $item['enterprise_id'] = session('rootId');
                $success = $this->userModel->create($item);
                if ($success->id) {
                    //详情表
                    $user_detail[$key]['uid'] = $success->id;
                    $user_detail[$key]['enterprise_id'] = session('rootId');
                    $this->userdetailModel->create($user_detail[$key]);
                    $s++;
                }
            }
            //响应
            $this->success("总{$sumCount}条，成功{$s}条", '/enterprise/users/academy');
        } else {
            $this->success($file->getError(), '/enterprise/users/academy');
        }
    }

    public function academyverificationData($paramArray)
    {
        //获取当前ID
        $enterpriseId = session('rootId');
        //删除重复数组 删除个数记录下返回给用户
        $ii = 1;
        foreach ($paramArray as $key => $array) {
            $ii++;
            //验证姓名合法性
            $preg_name = '/^[\x{4e00}-\x{9fa5}]{2,10}$|^[a-zA-Z\s]*[a-zA-Z\s]{2,20}$/isu';
            if (!preg_match($preg_name, $array['name'])) {
                return $this->success('导入失败:第' . $ii . '行，姓名不合法', '/enterprise/users/academy');
            }
            //验证手机号唯一性
            $onlyphone = $this->userModel->onlyUser($array['phone'], 'phone');
            if ($onlyphone) {
                return $this->success('导入失败:第 ' . $ii . '行，存在重复手机号', '/enterprise/users/academy');
            }
            //验证身份证唯一性
            $onlyphone = $this->userdetailModel->onlyUserdetail($array['idnumber'], 'idnumber');
            if ($onlyphone) {
                return $this->success('导入失败:第' . $ii . '行，存在重复身份证', '/enterprise/users/academy');
            }
            //验证数据是否为空
            if (empty($array['name']) || empty($array['idnumber']) || empty($array['phone']) || empty($array['password']) || empty($array['subjects_id'])) {
                return $this->success('导入失败:第' . $ii . '行，数据不能为空', '/enterprise/users/academy');
            }
        }
        //验证数据格式
        $validate = new Validate($this->validate, $this->msg);
        $i = 1;
        foreach ($paramArray as $key => $item) {
            $i++;
            if (!$validate->check($item)) {
                return $this->success('导入失败: ' . $i . '行' . $validate->getError(), '/enterprise/users/academy');
            }
            if (!validation_filter_id_card($item['idnumber'])) {
                return $this->success('导入失败: ' . $i . '行,身份证真实性错误', '/enterprise/users/academy');
            }
        }
        return $paramArray;
    }

    /**
     * Excel批量上传员工
     * @param Request $request
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function batch(Request $request)
    {
        //获取ID
        $enterpriseId = session('rootId');
        //判断文件夹是否存在不存在则创建
        if (!file_exists(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId)) {
            mkdir(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId, 0777, true);
        }
        //处理Excel文件请求
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $file = request()->file('file');
        if (is_null($file)) {
            return $this->success("上传文件不能为空~", '/enterprise/users/batchIndex');
        }
        $info = $file->validate(['size' => 512000, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId);

        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $enterpriseId . DS . $exclePath;   //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
            echo "<pre>";
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            array_shift($excel_array);  //删除第一个数组(标题);
            //将数据拼接成数组
            $sumCount = 0;
            $data = [];
            foreach ($excel_array as $k => $v) {
                $data[$k]['name'] = trim($v[0]); //员工姓名
                $data[$k]['idnumber'] = trim($v[1]); //身份证
                $data[$k]['phone'] = trim($v[2]); //手机号
                $data[$k]['password'] = trim($v[3]); //密码
                $data[$k]['create_time'] = trim(date('Y-m-d '));//时间
                $data[$k]['type'] = '0';//企业类型
                $sumCount++;
            }
            //验证导入总数不能大于企业限制数量1000人
            $resultCount = $this->userModel->where('enterprise_id', session('rootId'))->count();
            $requestCount = 1000 - $resultCount;
            if ($sumCount > $requestCount) {
                $totalSum = $sumCount - $requestCount;
                return $this->success("本次导入人数超出{$totalSum}条,导入失败。<br/>如有疑问请联系我们，联系方式010－57137487", '/enterprise/users/batchIndex');
            }
            //删除空数据
            foreach ($data as $k => $v) {
                if (preg_match("/\s/", $v['name'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['idnumber'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['phone'])) {
                    unset($data[$k]);
                }
                if (preg_match("/\s/", $v['password'])) {
                    unset($data[$k]);
                }
            }
            //验证导入的数据真实性
            $newArray = $this->verificationData($data);
            if (!$newArray) {
                return $this->success("请检查数据有效性,重新上传~", '/enterprise/users/batchIndex');
            }
            //拼接成两个数组，分别插入数据
            $user = [];
            $user_detail = [];
            $i = 0;
            foreach ($newArray as $k => $v) {
                $user_detail[$k]['name'] = $v['name']; //员工姓名
                $user_detail[$k]['idnumber'] = $v['idnumber']; //身份证
                $user[$k]['phone'] = $v['phone']; //手机号
                $user[$k]['type'] = $v['type']; //企业
                $user[$k]['password'] = md5($v['password']); //密码
                $user_detail[$k]['create_time'] = date('Y-m-d '); //导入时间
                $i++;
            }
            $phone = array_column($user, 'phone');
            $idnumber = array_column($user_detail, 'idnumber');

            $repeat_arr = repeat_arr($phone);
            $repeat_num = implode(array_values($repeat_arr), '，');

            $repeat_idnumarr = repeat_arr($idnumber);
            $repeat_idnum = implode(array_values($repeat_idnumarr), '，');

            if (!empty($repeat_arr)) {
                return $this->success('导入失败: 表格中' . $repeat_num . '手机号重复', '/enterprise/users/batchIndex');
            };

            //验证表格身份证号唯一性
            if (!empty($repeat_idnum)) {
                return $this->success('导入失败: 表格中存在重复身份证号，为：' . $repeat_idnum, '/enterprise/users/batchIndex');
            }
            //数据添加操作
            $s = 0;
            foreach ($user as $key => $item) {
                $item['enterprise_id'] = session('rootId');
                $success = $this->userModel->create($item);
                if ($success->id) {
                    //员工详情表
                    $user_detail[$key]['uid'] = $success->id;
                    $user_detail[$key]['enterprise_id'] = session('rootId');
                    $this->userdetailModel->create($user_detail[$key]);
                    $s++;
                }
            }
            //响应
            $this->success("总{$sumCount}条，成功{$s}条", '/enterprise/users/batchIndex');
        } else {
            $this->success($file->getError(), '/enterprise/users/batchIndex');
        }
    }

    /*
     * 处理验证数据
     * @param array Excel取出的数据
     * return array
     * */
    public function verificationData($paramArray)
    {
        //获取当前ID
        $enterpriseId = session('rootId');
        //删除重复数组 删除个数记录下返回给用户
        $ii = 1;
        foreach ($paramArray as $key => $array) {
            $ii++;
            //验证姓名合法性
            $preg_name = '/^[\x{4e00}-\x{9fa5}]{2,10}$|^[a-zA-Z\s]*[a-zA-Z\s]{2,20}$/isu';
            if (!preg_match($preg_name, $array['name'])) {
                return $this->success('导入失败:第' . $ii . '行，姓名不合法', '/enterprise/users/batchIndex');
            }
            //验证手机号唯一性
            $onlyphone = $this->userModel->onlyUser($array['phone'], 'phone');
            if ($onlyphone) {
                return $this->success('导入失败:第 ' . $ii . '行，存在重复手机号', '/enterprise/users/batchIndex');
            }
            //验证身份证唯一性
            $onlyphone = $this->userdetailModel->onlyUserdetail($array['idnumber'], 'idnumber');
            if ($onlyphone) {
                return $this->success('导入失败:第' . $ii . '行，存在重复身份证', '/enterprise/users/batchIndex');
            }
            //验证数据是否为空
            if (empty($array['name']) || empty($array['idnumber']) || empty($array['phone']) || empty($array['password'])) {
                return $this->success('导入失败:第' . $ii . '行，数据不能为空', '/enterprise/users/batchIndex');
            }
        }
        //验证数据格式
        $validate = new Validate($this->validate, $this->msg);
        $i = 1;
        foreach ($paramArray as $key => $item) {
            $i++;
            if (!$validate->check($item)) {
                return $this->success('导入失败: ' . $i . '行' . $validate->getError(), '/enterprise/users/batchIndex');
            }
            if (!validation_filter_id_card($item['idnumber'])) {
                return $this->success('导入失败: ' . $i . '行,身份证真实性错误', '/enterprise/users/batchIndex');
            }
        }
        return $paramArray;
    }

}