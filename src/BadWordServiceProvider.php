<?php

namespace Patoui\LaravelBadWord;

use Illuminate\Support\ServiceProvider;
use Patoui\LaravelBadWord\Validation\BadWord as BadWordValidator;

class BadWordServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bad-word.php', 'bad-word');
    }
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['validator']->extend('bad_word', BadWordValidator::class . '@validate');

        $this->publishes([
            __DIR__.'/../config/bad-word.php' => config_path('bad-word.php')
        ], 'config');
    }
}
