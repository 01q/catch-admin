<?php

namespace catchAdmin\permissions;

use catchAdmin\permissions\model\Permissions;
use catchAdmin\system\model\OperateLog;
use catcher\CatchAdmin;
use think\facade\Db;

class OperateLogEvent
{
    public function handle($params)
    {
        $permission = $params['permission'];

        $parentPermission = Permissions::where('id', $permission->parent_id)->value('permission_name');

        $requestParams = request()->param();

        app(OperateLog::class)->storeBy([
            'creator_id' => $params['creator_id'],
            'module'     => $parentPermission ? : '',
            'method'     => request()->method(),
            'operate'    => $permission->permission_name,
            'route'      => $permission->permission_mark,
            'params'     => !empty($requestParams) ? json_encode($requestParams, JSON_UNESCAPED_UNICODE) : '',
            'created_at' => time(),
            'ip'         => request()->ip(),
        ]);
    }
}
