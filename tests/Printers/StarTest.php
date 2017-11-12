<?php

namespace Posprint\Tests\Printers;

/**
 * Unit Tests for Star Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Printers\Star;
use PHPUnit\Framework\TestCase;

class StarTest extends TestCase
{
    public function testInitialize()
    {
        $printer = new Star();
        $this->assertInstanceOf(Star::class, $printer);
    }
}
