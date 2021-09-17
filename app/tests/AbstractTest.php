<?php

namespace App\Tests;

use Framework\Tests\AbstractTest as Base;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class AbstractTest extends Base
{
    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        $this->app = require __DIR__ . '/../bootloader.php';
    }

    /**
     * This method is called after each test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->app);
        unset($this->response);
    }
}
