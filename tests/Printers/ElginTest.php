<?php

namespace Posprint\Tests\Printers;

/**
 * Unit Tests for Elgin Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Printers\Elgin;
use PHPUnit\Framework\TestCase;

class ElginTest extends TestCase
{
    public function testInitialize()
    {
        $printer = new Elgin();
        $this->assertInstanceOf(Elgin::class, $printer);
    }
}
