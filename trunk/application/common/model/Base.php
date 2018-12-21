<?php

namespace app\common\model;

use think\Model;

/**
 * model鸡肋
 * Class Base
 * @package app\common\model
 */
class Base extends Model
{
    protected $autoWriteTimestamp = false;
    //////////////////////////////////////////////单表查询//////////////////////////////////////////////////////
    /**
     * 查找一条数据
     * @param array $field
     * @param array $param
     * @param string $order
     * @return array|false|\PDOStatement|string|Model
     */
    public function BaseFind($param = [], $field = [], $order = '')
    {
        try {
            if ($message = checkArray($field, $param)) {
                throw new \exception($message);
            } else {
                $res['data'] = self::where($param)->field($field)->order($order)->find();
            }
        } catch (\exception $e) {
            $res['message'] = $e->getMessage();
            $res['error'] = true;
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    /**
     * 普通的查找数据
     * @param array $param
     * @param array $field
     * @param string $order
     * @param string $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function BaseSelect($param = [], $field = [], $order = '', $limit = "")
    {
        try {
            if ($message = checkArray($field, $param)) {
                throw new \exception($message);
            } else {
                $res['data'] = self::where($param)->field($field)->order($order)->limit($limit)->select();
            }
        } catch (\exception $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    /**
     * 普通查询分页
     * 第一个参数为数组 共有三个值
     * 第一个为分页条数,
     * 第二个为是否是简介模式(false/true)
     * 第三个为分页条件跟随 ['query'=>request()->param()]
     * 文档详见 https://www.kancloud.cn/manual/thinkphp5/154294
     * view里获取分页 {$list->render()} 无需赋值
     * @param array $param
     * @param array $field
     * @param array $order
     * @param array $paginate
     * @return string
     */
    public function BaseSelectPage($paginate, $param = [], $field = [], $order = '')
    {
        try {
            if ($message = checkArray($field, $param, $paginate)) {
                throw new \exception($message);
            } else {

                !empty($paginate[1]) ?: $paginate[1] = false;
                if (!empty($paginate[2])) {
                    list($listRows, $simple, $config) = $paginate;
                    $res['data'] = self::where($param)->field($field)->order($order)->paginate($listRows, $simple, $config);
                } else {
                    list($listRows, $simple) = $paginate;
                    $res['data'] = self::where($param)->field($field)->order($order)->paginate($listRows, $simple);
                }
            }
        } catch (\exception $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    /**
     * 查询数据个数
     * @param array $param
     * @return int|string
     */
    public function BaseSelectCount($param = [])
    {
        try {
            if ($message = checkArray($param)) {
                throw new \exception($message);
            } else {
                $res['data'] = self::where($param)->count();
            }
        } catch (\exception $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    ////////////////////////////////////////////关联模型////////////////////////////////////////////////////////

    /**
     * 模型关联查找一条数据
     * 三个参数均要求为数组
     * @param string $with
     * @param array $field
     * @param array $param
     * @param string $order
     * @return array|false|\PDOStatement|string|Model
     */
    public function BaseWithFind($with, $param = [], $field = [], $order = '')
    {
        try {
            if (!is_string($with)) {
                throw new \exception('关联模型为字符串且用","分割');
            } elseif ($message = checkArray($field, $param)) {
                throw new \exception($message);
            } else {
                $res['data'] = self::where($param)->with($with)->field($field)->order($order)->find();
            }
        } catch (\exception $e) {
            $res['message'] = $e->getMessage();
            $res['error'] = true;
        }
        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    /**
     * 模型关联查找数据数据
     * @param string $with 关联模型
     * @param array $param
     * @param array $field
     * @param string $order
     * @param string $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function BaseWithSelect($with, $param = [], $field = [], $order = '', $limit = "")
    {
        try {
            if (!empty($with) && !is_string($with)) {
                throw new \exception('关联模型为字符串且用","分割');
            } elseif ($message = checkArray($field, $param)) {
                throw new \exception($message);
            } else {
                $res['data'] = self::where($param)->with($with)->field($field)->order($order)->limit($limit)->select();
            }
        } catch (\exception $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    /**
     * 模型关联查询分页
     * 第一个参数为数组 共有三个值
     * 第一个为分页条数,
     * 第二个为是否是简介模式(false/true)
     * 第三个为分页条件跟随 ['query'=>request()->param()]
     * 文档详见 https://www.kancloud.cn/manual/thinkphp5/154294
     * view里获取分页 {$list->render()} 无需赋值
     * @param array $param
     * @param array $with
     * @param array $field
     * @param string $order
     * @param array $paginate
     * @return string
     */
    public function BaseWithSelectPage($paginate, $with, $param = [], $field = [], $order = '')
    {
        try {
            if ($message = checkArray($field, $param, $paginate)) {
                throw new \exception($message);
            } else {
                !empty($paginate[1]) ?: $paginate[1] = false;
                if (!empty($paginate[2])) {
                    list($listRows, $simple, $config) = $paginate;
                    $res['data'] = self::where($param)->with($with)->field($field)->order($order)->paginate($listRows, $simple, $config);
                } else {
                    list($listRows, $simple) = $paginate;
                    $res['data'] = self::where($param)->with($with)->field($field)->order($order)->paginate($listRows, $simple);
                }
            }
        } catch (\exception $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    /**
     * 模型关联查询数据个数
     * @param $with
     * @param array $param
     * @return int|string
     */
    public function BaseWithSelectCount($with, $param = [])
    {
        try {
            if ($message = checkArray($param)) {
                throw new \exception($message);
            } else {
                $res['data'] = self::where($param)->with($with)->count();
            }
        } catch (\exception $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    ////////////////////////////////////////////联表模型////////////////////////////////////////////////////////

    /**
     * 关联查询单条数据
     * @param string $alias
     * @param array $join
     * @param array $param
     * @param array $field
     * @param string $order
     * @return string
     */
    public function BaseJoinFind($alias, $join, $param = [], $field = [], $order = '')
    {
        try {
            if (empty($alias) || empty($join)) {
                throw new \exception('请对当前表起别名,同时传递链接参数');
            } elseif ($message = checkArray($field, $param)) {
                throw new \exception($message);
            } else {
                $res['data'] = self::where($param)->alias($alias)->join($join)->field($field)->order($order)->find();
            }
        } catch (\exception $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    /**
     * 连表查询多条数据
     * @param $alias
     * @param $join
     * @param array $param
     * @param array $field
     * @param string $order
     * @param string $limit
     * @return string
     */
    public function BaseJoinSelect($alias, $join, $param = [], $field = [], $order = '', $limit = "")
    {
        /**
         *  $join = array(
         *       ['__USER__ user', 'test.tid=user.uid'],
         *       ['__USER_DETAIL__ userdetail', 'user.uid=userdetail.uid'],
         *  );
         */
        try {
            if (empty($alias) || empty($join)) {
                throw new \exception('请对当前表起别名,同时传递链接参数');
            } elseif ($message = checkArray($field, $param)) {
                throw new \exception($message);
            } else {
                $res['data'] = self::where($param)->alias($alias)->join($join)->field($field)->order($order)->limit($limit)->select();
            }
        } catch (\exception $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    /**
     * 连表查询分页
     * 第一个参数为数组 共有三个值
     * 第一个为分页条数,
     * 第二个为是否是简介模式(false/true)
     * 第三个为分页条件跟随 ['query'=>request()->param()]
     * 文档详见 https://www.kancloud.cn/manual/thinkphp5/154294
     * view里获取分页 {$list->render()} 无需赋值
     * @param $paginate
     * @param $alias
     * @param $join
     * @param array $param
     * @param array $field
     * @param string $order
     * @return string|\think\paginator\Collection
     */
    public function BaseJoinSelectPage($paginate, $alias, $join, $param = [], $field = [], $order = '')
    {
        try {
            if (empty($alias) || empty($join)) {
                throw new \exception('请对当前表起别名,同时传递链接参数');
            } elseif ($message = checkArray($field, $param, $paginate)) {
                throw new \exception($message);
            } else {
                !empty($paginate[1]) ?: $paginate[1] = false;
                if (!empty($paginate[2])) {
                    list($listRows, $simple, $config) = $paginate;
                    $res['data'] = self::where($param)->alias($alias)->join($join)->field($field)->order($order)->paginate($listRows, $simple, $config);
                } else {
                    list($listRows, $simple) = $paginate;
                    $res['data'] = self::where($param)->alias($alias)->join($join)->field($field)->order($order)->paginate($listRows, $simple);
                }
            }
        } catch (\exception $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    /**
     * 连表查询数据个数
     * @param $alias
     * @param $join
     * @param array $param
     * @return int|string
     */
    public function BaseJoinSelectCount($alias, $join, $param = [])
    {
        try {
            if ($message = checkArray($param)) {
                throw new \exception($message);
            } else {
                $res['data'] = self::where($param)->alias($alias)->join($join)->count();
            }
        } catch (\exception $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }

        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }

    ////////////////////////////////////////////数据操作////////////////////////////////////////////////////////

    /**
     * 保存数据
     * @param $data
     * @param bool $allowfield
     * @param string $key
     * @return mixed|string
     * @user 李海江 2018/7/13~上午11:31
     */
    public function BaseSave($data, $allowfield = true, $key = 'id')
    {
        try {
            if (!is_array($data) || empty($data)) {
                throw new \exception('插入的数据必须为数组的形式且不能为空');
            } else {
                $res = $this->allowField($allowfield)->save($data);
            }
        } catch (\exception $e) {
            $res['message'] = $e->getMessage();
            $res['error'] = true;
        }

        if (empty($res['error'])) {
            return $this->$key;
        } else {
            die($res['message']);
        }
    }

    /**
     * 数据更新
     * @param array $data
     * @param  array $updateParam
     * @param bool $allowfield
     * @return false|int|string
     * @user 李海江 2018/7/13~下午2:59
     */
    public function BaseUpdate($data, $updateParam, $allowfield = true)
    {
        try {
            if (!is_array($data) || empty($data)) {
                throw new \exception('更新数据需要为数组格式');
            } elseif (!is_array($updateParam) || empty($updateParam)) {
                throw new \exception('更新数据需要为数组格式');
            } else {
                $res = $this->allowField($allowfield)->isUpdate(true)->save($data, $updateParam);
            }
        } catch (\exception $e) {
            $res['message'] = $e->getMessage();
            $res['error'] = true;
        }
        if (empty($res['error'])) {
            return $res;
        } else {
            die($res['message']);
        }
    }

    /**
     * 批量新增 | 批量修改
     * @param array 数据
     * @param bool $update 是否自动识别更新和写入
     * @return int|string
     */
    public function BaseSaveAll($arrdata, $update = true)
    {
        try {
            if (count($arrdata) == count($arrdata, 1)) {
                throw new \exception('单条数据请使用BaseSave方法');
            } elseif (!is_array($arrdata) || empty($arrdata)) {
                throw new \exception('插入的数据必须为数组的形式切不能为空');
            } else {
                $res = $this->saveAll($arrdata, $update);
            }
        } catch (\exception $e) {
            $res['message'] = $e->getMessage();
            $res['error'] = true;
        }

        if (empty($res['error'])) {
            return count($res);
        } else {
            die($res['message']);
        }
    }

    /**
     * 删除数据
     * 需要传递删除条件
     * @param array $param
     * @return int|string
     */
    public function BaseDelete($param)
    {
        try {
            if (empty($param)) {
                throw new \exception("无条件不允许进行删除操作");
            } elseif ($message = checkArray('', $param)) {
                throw new \exception($message);
            } else {
                $res['data'] = self::where($param)->delete();
            }
        } catch (\exception $e) {
            $res['message'] = $e->getMessage();
            $res['error'] = true;
        }
        if (empty($res['error'])) {
            return $res['data'];
        } else {
            die($res['message']);
        }
    }
}