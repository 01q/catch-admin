<?php
namespace catchAdmin;

use catchAdmin\login\LoginLogListener;
use catchAdmin\permissions\OperateLogListener;
use catchAdmin\permissions\PermissionsMiddleware;
use catchAdmin\system\event\LoginLogEvent;
use catchAdmin\system\event\OperateLogEvent;
use catcher\CatchQuery;
use catcher\command\BackupCommand;
use catcher\command\CompressPackageCommand;
use catcher\command\CreateModuleCommand;
use catcher\command\InstallCommand;
use catcher\command\MigrateCreateCommand;
use catcher\command\MigrateRollbackCommand;
use catcher\command\MigrateRunCommand;
use catcher\command\ModelGeneratorCommand;
use catcher\command\ModuleCacheCommand;
use catcher\command\SeedRunCommand;
use catcher\command\worker\WsWorkerCommand;
use catcher\event\LoadModuleRoutes;
use catcher\validates\Sometimes;
use think\facade\Validate;
use think\Service;

class CatchAdminService extends Service
{
    /**
     *
     * @time 2019年11月29日
     * @return void
     */
    public function boot()
    {
        $this->registerCommands();
        $this->registerValidates();
        $this->registerMiddleWares();
        $this->registerEvents();
        $this->registerListeners();
        $this->registerQuery();
    }

    /**
     *
     * @time 2019年12月13日
     * @return void
     */
    protected function registerCommands(): void
    {
        $this->commands([
            InstallCommand::class,
            ModuleCacheCommand::class,
            MigrateRunCommand::class,
            ModelGeneratorCommand::class,
            SeedRunCommand::class,
            BackupCommand::class,
            CompressPackageCommand::class,
            CreateModuleCommand::class,
            MigrateRollbackCommand::class,
            MigrateCreateCommand::class,
            WsWorkerCommand::class,
        ]);
    }
    /**
     *
     * @time 2019年12月07日
     * @return void
     */
    protected function registerValidates(): void
    {
        $validates = config('catch.validates');

        Validate::maker(function($validate) use ($validates) {
            foreach ($validates as $vali) {
                $vali = app()->make($vali);
                $validate->extend($vali->type(), [$vali, 'verify'], $vali->message());
            }
        });
    }

    /**
     *
     * @time 2019年12月12日
     * @return void
     */
    protected function registerMiddleWares(): void
    {
        $this->app->middleware->import([
           'catch_check_permission' => PermissionsMiddleware::class,
        ], 'route');
    }

    /**
     *
     * @time 2019年12月12日
     * @return void
     */
    protected function registerEvents(): void
    {
        $this->app->event->bind([
            'loginLog' => LoginLogEvent::class,
            'operateLog' => OperateLogEvent::class,
        ]);
    }

    /**
     * 注册监听者
     *
     * @time 2019年12月12日
     * @return void
     */
    protected function registerListeners(): void
    {
        $this->app->event->listenEvents([
            'loginLog' => [
                LoginLogListener::class,
            ],
            'operateLog' => [
                OperateLogListener::class,
            ],
            'RouteLoaded' => [
                LoadModuleRoutes::class
            ],
        ]);
    }

    protected function registerQuery()
    {
        $connections = $this->app->config->get('database.connections');

        $connections['mysql']['query'] = CatchQuery::class;

        $this->app->config->set([
          'connections' => $connections
        ], 'database');
    }
}
