<?php

namespace Tests\Features\Http;

use Tests\TestCase;
use App\Helper\Logger;

class ExceptionTest extends TestCase
{
    public function test_HttpNotFoundException(): void
    {
        $fileName = Logger::getFilename();

        $size = filesize($fileName);
        $this->get('/non-existing-route');
        $sizeAfter = filesize($fileName);
        
        $this->assertSame($size, $sizeAfter);
    }
}