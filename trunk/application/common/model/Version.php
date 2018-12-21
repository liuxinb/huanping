<?php
namespace app\common\model;

use app\api\controller\Pro;

/**
 * Class Version
 * @package app\common\model
 */
class Version extends Base
{
    /**
     * 检查APP版本
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function checkversion($data)
    {
        $validate = Validate('Version');
        if (!$validate->check($data)) {
            result((string)$validate->getError());
        }
        $version = $this
            ->where('type', $data['type'])
            ->order('num', 'DESC')
            ->find();
        if ($version->num > $data['num']) {
            $data = array(
                'latestName' => $version['name'],
                'latestVersion' => $version->num,
                'update' => true,
                'url' => $version->url,
                'mustupdate' => empty($version->mustupdate) ? false : true,
            );
            Pro::$message['20021']['data'] = $data;
            result('20021');
        } else {
            result('40042');
        }
    }
}