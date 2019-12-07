<?php

$router->resource('user', '\catchAdmin\user\controller\User');
// 用户列表
$router->get('users', '\catchAdmin\user\controller\User/list');
// 切换状态
$router->put('user/switch/status/<id>', '\catchAdmin\user\controller\User/switchStatus');
$router->put('user/recover/<id>', '\catchAdmin\user\controller\User/recover');
