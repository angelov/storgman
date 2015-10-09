<?php

use Way\Tests\ModelHelpers;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    use ModelHelpers;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
