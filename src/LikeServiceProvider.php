<?php

/*
 * This file is part of the overtrue/laravel-like.
 *
 * (c) overtrue <anzhengchao@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Overtrue\LaravelLike;

use Illuminate\Support\ServiceProvider;

/**
 * Class LikeServiceProvider.
 */
class LikeServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            \dirname(__DIR__).'/config/like.php' => config_path('like.php'),
        ], 'config');

        $this->publishes([
            \dirname(__DIR__).'/migrations/' => database_path('migrations'),
        ], 'migrations');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(\dirname(__DIR__).'/migrations/');
        }
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            \dirname(__DIR__).'/config/like.php', 'like'
        );
    }
}
