<?php

namespace Tests\Api;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class phpUnitTest extends TestCase
{
    #[Test]
    public function DoesPHPUnitTestWork(): void
    {
        $this->assertEquals(true, true);
    }
}
