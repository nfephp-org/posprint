<?php

namespace Posprint\Tests\Printers;

/**
 * Unit Tests for Sweda Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Printers\Sweda;

class SwedaTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialize()
    {
        $printer = new Sweda();
        $this->assertInstanceOf(Sweda::class, $printer);
    }
}
