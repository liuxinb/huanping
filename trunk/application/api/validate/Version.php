<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/6/13
 * Time: 下午2:50
 */

namespace app\api\validate;

use think\Validate;

class Version extends Validate
{
    /** 规则 **/
    protected $rule = [
        ['num', 'require', '40040'],
        ['type', 'require', '40041'],
    ];


}