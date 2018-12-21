<?php

namespace app\enterprise\controller;

use app\common\model\Record as RecordModel;
use app\common\controller\EnterBase;
use think\Request;
use \think\Validate;
use think\File;

class Record extends EnterBase
{
    private $recordModel;
    private $adminRole;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->recordModel = new RecordModel();
        $this->adminRole = parent::role();
    }

    protected $msg = [
        'firmname.require' => '企业名称不能为空',
        'registersite.require' => '注册地址不能为空',
        'invoicename.require' => '发票名称不能为空',
        'identifynumber.require' => '纳税人识别号不能为空',
        'identifynumber.max' => '纳税人识别号最长不能超出20位',
        'identifynumber' => '请输入正确的纳税人识别号',
        'addressphone.require' => '地址电话不能为空',
        'addressphone.max' => '地址电话长度不能超过11',
        'name.require' => '姓名不能为空',
        'phone.require' => '手机不能为空',
        'invoiceaddress.require' => '发票地址不能为空',
        'openingbank.require' => '开户行不能为空',
        'accountnumber.require' => '账号不能为空',
        'phone' => '手机号格式不正确',
        'email.require' => '邮箱不能为空',
        'email' => '邮箱格式不正确',
    ];

    protected $validateData = [
        'firmname' => 'require',
        'registersite' => 'require',
        'invoicename' => 'require',
        'invoiceaddress' => 'require',
        'openingbank' => 'require',
        'accountnumber' => 'require',
        'identifynumber' => 'require|max:20',
        'identifynumber' => ['/^((\d{6}[0-9A-Z]{9})|([0-9A-Za-z]{2}\d{6}[0-9A-Za-z]{10})|([0-9A-Za-z]{20}))$/'],
        'addressphone' => 'require|max:20',
        'name' => 'require',
        'phone' => ['/^1[3456789]\d{9}$/', 'require'],
        'email' => ['^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$', 'require'],
    ];
    /***
     *  档案页
     * @return \think\response\View
     */
    public function index()
    {
        return view('record/index');
    }
    /***
     * 添加档案
     * @param Request $request
     * @return string
     */
    public function add(Request $request)
    {
        //获取当前企业ID
        $enterpriseId = session('rootId');
        //验证提交记录
        $recordfrequency = $this->recordModel->recordFrequency($enterpriseId);
        if ($recordfrequency > 0) {
            echo '该企业下档案已提交过，不可重复提交!';die;
        }
        //验证数据格式
        $validate = new Validate($this->validateData, $this->msg);
        $data = $request->param();
        //验证省市级
        if ($data['city'] == '地级市' || $data['city'] == '地级市' || $data['county'] == '市、县级市') {
            echo '省、市、县未选中，或者选中有误';die;
        }
        if (!$validate->check($data)) {
            echo $validate->getError();die;
        }
        //验证发票名称
        if (!preg_match('/^[\(\)\x{4e00}-\x{9fa5}A-Z_]+$/u', $data['invoicename'])) {
            echo '发票名称不符合规则，只支持中文，大写英文，英文小括弧和下划线';die;
        }
        //处理数据
        $dataArray =
            [
                'id' => $data['id'],
                'firmname' => $data['firmname'],
                'province' => $data['province'],
                'city' => $data['city'],
                'county' => $data['county'],
                'registersite' => $data['registersite'],
                'invoicename' => $data['invoicename'],
                'identifynumber' => $data['identifynumber'],
                'addressphone' => $data['addressphone'],
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'enterId' => $enterpriseId,
                'invoiceaddress' => $data['invoiceaddress'],
                'openingbank' => $data['openingbank'],
                'accountnumber' => $data['accountnumber']
            ];
        //档案添加操作
        $recordresult = $this->recordModel->recordCreate($dataArray);
        //跳转编辑页
        if ($recordresult) {
            return 'success';
        } else {
            return '';
        }
    }

    /***
     * 返回要修改数据
     * @param string    企业id
     * @return \think\response\View|void
     * @throws \think\Exception\DbException
     */
    public function update($enterpriseId = '')
    {
        //获取当前企业ID
        !empty($enterpriseId) ?: $enterpriseId = session('rootId');
        //验证提交记录
        $recordfrequency = $this->recordModel->recordFrequency($enterpriseId);
        if ($recordfrequency <= 0) {
            return $this->redirect('/recordadd');
        }
        //查询
        $recorddata = $this->recordModel->get(['enterId' => $enterpriseId]);
        //响应
        return view('record/update', ['data' => $recorddata]);
    }

    /***
     * 档案数据保存
     * @param Request $request
     * @return string
     */
    public function save(Request $request)
    {
        //验证数据格式
        $validate = new Validate($this->validateData, $this->msg);
        $data = $request->param();
        //验证省市级
        if ($data['city'] == '地级市' || $data['city'] == '地级市' || $data['county'] == '市、县级市') {
            echo '省、市、县未选中，或者选中有误';
            die;
        }
        if (!$validate->check($data)) {
            echo $validate->getError();
            die;
        }
        //验证发票名称
        if (!preg_match('/^[\(\)\x{4e00}-\x{9fa5}A-Z_]+$/u', $data['invoicename'])) {
            echo '发票名称不符合规则，只支持中文，大写英文，英文小括弧和下划线';
            die;
        }
        //处理数据
        $dataArray = [
            'id' => $data['id'],
            'firmname' => $data['firmname'],
            'province' => $data['province'],
            'city' => $data['city'],
            'county' => $data['county'],
            'registersite' => $data['registersite'],
            'invoicename' => $data['invoicename'],
            'identifynumber' => $data['identifynumber'],
            'addressphone' => $data['addressphone'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'invoiceaddress' => $data['invoiceaddress'],
            'openingbank' => $data['openingbank'],
            'accountnumber' => $data['accountnumber']
        ];
        $recordresulr = $this->recordModel->update($dataArray);
        if ($recordresulr) {
            return '更新成功~';
        } else {
            return '更新失败~';
        }
    }

    /***
     *  身份证文件上传
     * @param Request $request
     * @return string
     */
    public function upload(Request $request)
    {
        //请求
        $file = $request->file('fileupload');
        //移动文件/public/uploads/目录
        $uploadDir = DS . 'uploads' . DS .'certificates' . DS;
        $info = $file->validate(['ext' => 'jpg,png,jpeg'])->move(ROOT_PATH . 'public' . $uploadDir);
        //将上传文件的路径返回
        if ($info) {
            return $uploadDir . $info->getSaveName();
        } else {
            echo 404;die;
        }
    }

}