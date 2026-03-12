<?php

namespace Tests\Unit;

use Tests\TestCase;

class FunctionsTest extends TestCase
{
    public function test_getRootPath_ok(): void
    {
        $expected = dirname(__DIR__, 2);
        $this->assertEquals($expected, getRootPath());
    }

    public function test_prepareEnv_ok(): void
    {
        $rootDir = getRootPath();
        $envPath = "$rootDir/.env";

        $this->assertFileExists($envPath);
    }

    public function test_env_ok(): void
    {
        $expectedUsername = env('USERNAME');
        $expectedPassword = env('PASSWORD');

        $this->assertNotEmpty($expectedUsername);
        $this->assertNotEmpty($expectedPassword);
    }
}