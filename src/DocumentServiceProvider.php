<?php

namespace Wealedger\Document;

use Illuminate\Support\ServiceProvider;

class DocumentServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';

        $this->loadViewsFrom(__DIR__ . '/views', 'document');// 指定视图目录

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/document'),// 发布视图目录到resources 下
            __DIR__.'/assets' => public_path('vendor/document'),// 发布资源文件到public下
            __DIR__.'/config/document.php' => config_path('document.php'), // 发布配置文件到 laravel 的config 下
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        config([
            'config/document.php',
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['document'];
    }
}
