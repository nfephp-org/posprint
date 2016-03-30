<?php

namespace Posprint\Tests\Printers;

/**
 * Unit Tests for Star Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Printers\Star;

class StarTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialize()
    {
        $printer = new Star();
        $this->assertInstanceOf(Star::class, $printer);
    }
}
