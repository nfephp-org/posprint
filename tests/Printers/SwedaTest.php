<?php

namespace Posprint\Tests\Printers;

/**
 * Unit Tests for Sweda Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Printers\Sweda;
use PHPUnit\Framework\TestCase;

class SwedaTest extends TestCase
{
    public function testInitialize()
    {
        $printer = new Sweda();
        $this->assertInstanceOf(Sweda::class, $printer);
    }
}
