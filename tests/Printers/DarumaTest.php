<?php

namespace Posprint\Tests\Printers;

/**
 * Unit Tests for Daruma Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Printers\Daruma;

class DarumaTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialize()
    {
        $printer = new Daruma();
        $this->assertInstanceOf(Daruma::class, $printer);
    }
}
