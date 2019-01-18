<?php

namespace Patoui\LaravelBadWord\Test;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Patoui\LaravelBadWord\BadWordServiceProvider;

abstract class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('bad-word', [
            'en' => ['badword'],
            'fr' => ['mauvais']
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $application
     * @return array
     */
    protected function getPackageProviders($application)
    {
        return [BadWordServiceProvider::class];
    }
}
