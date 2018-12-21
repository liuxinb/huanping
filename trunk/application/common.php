<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
error_reporting(E_ERROR | E_PARSE);

// 应用公共文件
//生成随机唯一订单号
function get_order_num($firmId)
{
    ini_set('date.timezone', 'Asia/Shanghai');
    $m = explode(' ', microtime());
    $time = ($m[1] . ($m[0] * 1000)) . "";
    $exTime = explode('.', $time);
    return (sprintf("HP" . $exTime[0] . $firmId));
}

function dd($arr)
{
    echo "<pre>";
    print_r($arr);
    die;
}

function object_to_array($obj)
{
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }

    return $obj;
}

//$arr->传入数组   $key->判断的key值
function array_unset_tt($arr, $key)
{
    //建立一个目标数组
    $res = array();
    foreach ($arr as $value) {
        //查看有没有重复项

        if (isset($res[$value[$key]])) {
            //有：销毁

            unset($value[$key]);

        } else {

            $res[$value[$key]] = $value;
        }
    }
    return $res;
}


/**
 * @param $array
 */
function repeat_arr($arr)
{
    //数组去重
    $unique_arr = array_unique($arr);
    // 获取重复数据的数组
    $repeat_arr = array_diff_assoc($arr, $unique_arr);
    return $repeat_arr;
}

/**
 * 创建一个证书
 * @param string $content 证书内容
 * @param string $font 字体
 * @param string $date 时间
 * @param string $certificate_id 证书id
 * @param string $ImgCode 二维码
 * @return string
 */
function certificate($content, $font, $date, $certificate_id, $ImgCode, $name, $sex, $idnumber)
{

    //1.创建一个画布
    $big = imagecreatefromjpeg(config('APP_NAME') . '/static/zhengshu/back.jpg');
    //2.添加水印文字
    $black = imagecolorallocate($big, 0, 0, 0);

    $content_height = '350';

    for ($i = 0; $i < count($content); $i++) {
        imagettftext($big, 13, 0, 105, $content_height, $black, $font, $content[$i]);
        $content_height += 30;
    }
    //姓名
    switch (mb_strlen($name)){
        case 2:
            imagettftext($big, 12, 0, 170, 348, $black, $font,$name);
            break;
        case 3:
            imagettftext($big, 12, 0, 163, 348, $black, $font,$name);
            break;
        case 4:
            imagettftext($big, 12, 0, 156, 348, $black, $font,$name);
            break;
        case 5:
            imagettftext($big, 12, 0, 149, 348, $black, $font,$name);
            break;
        case 6:
            imagettftext($big, 12, 0, 140, 348, $black, $font,$name);
            break;
    }
    //性别
    imagettftext($big, 12, 0, 283, 348, $black, $font, $sex);
    //身份证号
    imagettftext($big, 12, 0, 351, 348, $black, $font, $idnumber);
    //时间
    imagettftext($big, 12, 0, 283, 660, $black, $font, $date);
    //证书编号
    imagettftext($big, 12, 0, 288, 262, $black, $font, $certificate_id);
    //创建二维码的实例
    $src = imagecreatefromstring(file_get_contents($ImgCode));
    //将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果
    imagecopymerge($big, $src, 140, 600, 0, 0, 115, 115, 100);

    /* 如果需要保存 */
    $filename = config('certificate.jpg') . md5(time()) . '.jpg';
    //证书文件
    $res = imagejpeg($big, $filename);

    @imagedestroy($src);
    @imagedestroy($big);
    if ($res) {
        return $filename;
    }

}

/**
 * 检查条件的规范性
 * @param array $field
 * @param array $param
 * @param array $order
 * @param array $paginate
 * @return bool|string
 */
function checkArray($field = [], $param = [], $paginate = [])
{
    $layout_array = "条件必须为数组";
    if (!empty($field) && !is_array($field)) {
        return 'field' . $layout_array;
    } elseif (!empty($param) && !is_array($param)) {
        return 'param' . $layout_array;
    } elseif (!empty($paginate) && !is_array($paginate)) {
        return 'paginate' . $layout_array;
    } else {
        return false;
    }
}

/**
 * 创建token
 * @return string $token
 */
function create_token()
{
    return md5(uniqid() . config('salt'));
}

/**
 * 检测验证码是否和服务器端一致
 * @param $code 验证码
 */
function check_code($code, $phone)
{
    if ($code != \think\Cache::get($phone) || $code == null) {
        return false;
    } else {
        //当验证码验证通过后从缓存中清除code
        \think\Cache::rm($phone);
        return true;
    }
}


/***
 * 一个数组我取出想要的键值对组成一个新的数组
 * @param $data  原数组
 * @param $table 想要的数组键名
 * @return array 得到的数组
 */
function create_mysqlData($data, $table)
{
    $arr = array();

    foreach ($data as $k => $v) {
        if (in_array($k, $table)) {
            $arr[$k] = $v;
        }
    }

    return $arr;
}

/***
 * 返回状态码信息
 * @param string $code 状态码
 * @param bool $type json类型
 */
function result($code, $type = true)
{

    if ($code == '40009') {
        header('isTokenValid: false');
        exit(json_encode(\app\api\controller\Pro::$message[$code], $type));
    }
    //exit 不可以换成return 不然不能正常返回
    header('isTokenValid: true');
    exit(json_encode(\app\api\controller\Pro::$message[$code], $type));
}


function subTime($str)
{
    //年
    $a = substr($str, 0, 4);
//月
    $b = substr($str, 4, 2);
//日
    $c = substr($str, 6, 2);
//时
    $d = substr($str, 8, 2);
//分
    $f = substr($str, 10, 2);
//秒
    $g = substr($str, 12, 2);
    $time = $a . "-" . $b . "-" . $c . " " . $d . ":" . $f . ":" . $g;

    return $time;
}


class AES
{
    protected $cipher;
    protected $mode;
    protected $pad_method;
    protected $secret_key;
    protected $iv;

    public function __construct($key, $method = 'pkcs7', $iv = '', $mode = MCRYPT_MODE_ECB, $cipher = MCRYPT_RIJNDAEL_128)
    {
        $this->secret_key = $key;

        $this->pad_method = $method;

        $this->iv = $iv;

        $this->mode = $mode;

        $this->cipher = $cipher;
    }

    function mcrypt_get_block_size($cipher, $mode)
    {
    }

    protected function pad_or_unpad($str, $ext)
    {
        if (!is_null($this->pad_method)) {

            $func_name = __CLASS__ . '::' . $this->pad_method . '_' . $ext . 'pad';

            if (is_callable($func_name)) {

                $size = mcrypt_get_block_size($this->cipher, $this->mode);

                return call_user_func($func_name, $str, $size);
            }
        }

        return $str;
    }

    protected function pad($str)
    {
        return $this->pad_or_unpad($str, '');
    }

    protected function unpad($str)
    {
        return $this->pad_or_unpad($str, 'un');
    }

    public function encrypt($str)
    {
        $str = $this->pad($str);

        $td = mcrypt_module_open($this->cipher, '', $this->mode, '');

        if (empty($this->iv)) {

            $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        } else {

            $iv = $this->iv;

        }

        mcrypt_generic_init($td, $this->secret_key, $iv);

        $cyper_text = mcrypt_generic($td, $str);

        $rt = base64_encode($cyper_text);

        mcrypt_generic_deinit($td);

        mcrypt_module_close($td);

        return $rt;
    }

    public function decrypt($str)
    {
        $td = mcrypt_module_open($this->cipher, '', $this->mode, '');

        if (empty($this->iv)) {

            $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        } else {

            $iv = $this->iv;

        }

        mcrypt_generic_init($td, $this->secret_key, $iv);

        $decrypted_text = mdecrypt_generic($td, base64_decode($str));

        $rt = $decrypted_text;

        mcrypt_generic_deinit($td);

        mcrypt_module_close($td);

        return $this->unpad($rt);
    }

    public static function pkcs7_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text . str_repeat(chr($pad), $pad);
    }

    public static function pkcs7_unpad($text)
    {
        $pad = ord($text[strlen($text) - 1]);

        if ($pad > strlen($text)) return false;

        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;

        return substr($text, 0, -1 * $pad);
    }
}

/***
 * 身份证真实性验证规则
 */
function validation_filter_id_card($id_card)
{
    if (strlen($id_card) == 18) {
        return idcard_checksum18($id_card);
    } elseif ((strlen($id_card) == 15)) {
        $id_card = idcard_15to18($id_card);
        return idcard_checksum18($id_card);
    } else {
        return false;
    }
}

// 计算身份证校验码，根据国家标准GB 11643-1999
function idcard_verify_number($idcard_base)
{
    if (strlen($idcard_base) != 17) {
        return false;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}

// 将15位身份证升级到18位
function idcard_15to18($idcard)
{
    if (strlen($idcard) != 15) {
        return false;
    } else {
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
            $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
        } else {
            $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
        }
    }
    $idcard = $idcard . idcard_verify_number($idcard);
    return $idcard;
}

// 18位身份证校验码有效性检查
function idcard_checksum18($idcard)
{
    if (strlen($idcard) != 18) {
        return false;
    }
    $idcard_base = substr($idcard, 0, 17);
    if (idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
        return false;
    } else {
        return true;
    }
}