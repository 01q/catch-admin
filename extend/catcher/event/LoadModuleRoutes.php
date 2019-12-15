<?php
declare (strict_types = 1);

namespace catcher\event;

use catcher\CatchAdmin;
use think\Route;

class LoadModuleRoutes
{
    /**
     * 处理
     *
     * @time 2019年11月29日
     * @return void
     */
    public function handle(): void
    {
        $router = app(Route::class);

        $domain = config('catch.domain');

        $routes = $this->getRoutes();

        if ($domain) {
            $router->domain($domain, function () use ($router, $routes) {
                foreach ($routes as $route) {
                    include $route;
                }
            });
        } else {
            $router->group(function () use ($router, $routes) {
                foreach ($routes as $route) {
                    include $route;
                }
            });
        }
    }

    /**
     *
     * @time 2019年12月15日
     * @return array
     */
    protected function getRoutes(): array
    {
        $routes = CatchAdmin::getRoutes();
        array_push($routes, CatchAdmin::directory() . 'login' . DIRECTORY_SEPARATOR . 'route.php');
        return $routes;
    }
}
