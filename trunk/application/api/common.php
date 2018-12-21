<?php

/**
 * 如果不是POST请求
 * @param $request
 */
function noIsPost($request)
{
    /** 如果不是post请求 **/
    if (!$request->isPost()) {
        //exit 不可以换成return 不然不能正常返回
        result('40001');
    }
}


/**
 * 给url路径加域名前缀的
 * @param array $arr
 * @param string $url url名
 * @param string $num even多维数组 odd一维数组
 * @return mixed 加完路径的数组
 */
function addPath($arr, $url, $num = 'even')
{
    if ($num == 'odd') {
        if (!empty($arr[$url])) {
            $arr[$url] = config('APP_NAME') . $arr[$url];
        }
    } else {
        for ($i = 0; $i < count($arr); $i++) {
            if (!empty($arr[$i][$url])) {
                $arr[$i][$url] = config('APP_NAME') . $arr[$i][$url];
            }
        }
    }
    return $arr;
}


function array_merge_rec(&$array, $key = "categoryid")
{  // 参数是使用引用传递的
    if (empty($array)) return false;
    // 定义一个新的数组
    $new_array = array();
    // 遍历当前数组的所有元素
    foreach ($array as $item) {
        if (is_array($item)) {
            // 如果当前数组元素还是数组的话，就递归调用方法进行合并
            array_merge_rec($item);
            // 将得到的一维数组和当前新数组合并
            $new_array = array_merge($new_array, $item);
        } else {
            // 如果当前元素不是数组，就添加元素到新数组中
            $new_array [] = $item[$key];
        }
    }
    // 修改引用传递进来的数组参数值
    $array = $new_array;
    return $array;
}