<?php

use Way\Tests\ModelHelpers;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    use ModelHelpers;

    /**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__.'/../../bootstrap/start.php';
    }

    public function tearDown()
    {
        Mockery::close();
    }

}
