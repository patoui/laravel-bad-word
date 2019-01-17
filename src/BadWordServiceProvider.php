<?php

namespace Patoui\LaravelBadWord;

use Illuminate\Support\ServiceProvider;
use Patoui\LaravelBadWord\Validation\BadWord;

class BadWordServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['validator']->extend('bad_word', BadWord::class . '@validate');

        $this->publishes([
            __DIR__.'/../config/bad-word.php' => config_path('bad-word.php')
        ], 'config');
    }
}
