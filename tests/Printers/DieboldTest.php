<?php

namespace Posprint\Tests\Printers;

/**
 * Unit Tests for Diebold Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Printers\Diebold;
use PHPUnit\Framework\TestCase;

class DieboldTest extends TestCase
{
    public function testInitialize()
    {
        $printer = new Diebold();
        $this->assertInstanceOf(Diebold::class, $printer);
    }
}
