<?php

namespace Posprint\Tests\Printers;

/**
 * Unit Tests for Elgin Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Printers\Elgin;

class ElginTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialize()
    {
        $printer = new Elgin();
        $this->assertInstanceOf(Elgin::class, $printer);
    }
}
