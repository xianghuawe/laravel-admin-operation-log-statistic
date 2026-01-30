<?php

namespace Xianghuawe\Admin;

use Illuminate\Console\Scheduling\Schedule;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\StatisticCommand::class,
        Console\SetStatisticCompanyCommand::class,
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'operation-statistic');

        if (file_exists($routes = admin_path('routes.php'))) {
            $this->loadRoutesFrom($routes);
        }

        $this->registerPublishing();
        $this->routes();
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config' => config_path()], 'operation-log-statistic-config');

            $this->publishes([__DIR__ . '/../resources/lang' => resource_path('lang')], 'operation-log-statistic-lang');

            $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'operation-log-statistic-migrations');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->commands($this->commands);

        // 注册定时任务调度（关键：向 Laravel 项目的调度器添加任务）
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            // 定义任务执行频率（例如每天凌晨3点）
            $schedule->command('admin-operation-log:statistic')
                ->dailyAt(config('admin.operation_log_statistic.daily_at'))
                ->when(function () {
                    return config('admin.operation_log_statistic.enable');
                })
                ->name('统计操作日志')
                ->onOneServer()
                ->withoutOverlapping();
        });
    }

    public function routes()
    {
        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        app('router')->group($attributes, function ($router) {

            /* @var \Illuminate\Support\Facades\Route $router */
            $router->namespace('\Xianghuawe\Admin\Controllers')->group(function ($router) {

                /* @var \Illuminate\Routing\Router $router */
                $router->resource('system/admin-operation-log-statistics', 'AdminOperationLogStatisticController', ['only' => 'index'])->names('admin.system.admin-operation-log-statistics');
            });
        });
    }
}
