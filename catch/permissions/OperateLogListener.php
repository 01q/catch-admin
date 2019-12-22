<?php

namespace catchAdmin\permissions;

use catchAdmin\permissions\model\Permissions;
use catcher\CatchAdmin;
use think\facade\Db;

class OperateLogListener
{
    public function handle($params)
    {
        $request = $params['request'];
        $permission = $params['permission'];

        $parentPermission = Permissions::where('id', $permission->parent_id)->value('permission_name');
        Db::name('operate_log')->insert([
            'creator_id' => $request->user()->id,
            'module'     => $parentPermission ? : '',
            'method'     => $request->method(),
            'operate'    => $permission->permission_name,
            'route'      => $permission->route,
            'params'     => json_encode($request->param()),
            'created_at' => time(),
            'ip'         => $request->ip(),
        ]);
    }
}
