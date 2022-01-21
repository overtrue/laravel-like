<?php

namespace Overtrue\LaravelLike;

use Illuminate\Support\ServiceProvider;

class LikeServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            \dirname(__DIR__) . '/config/like.php' => config_path('like.php'),
        ], 'config');

        $this->publishes([
            \dirname(__DIR__) . '/migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            \dirname(__DIR__) . '/config/like.php',
            'like'
        );
    }
}
