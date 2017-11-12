<?php

namespace Posprint\Tests\Connectors;

/**
 * Unit Tests for Network connector Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Connectors\Network;
use PHPUnit\Framework\TestCase;

class NetworkTest extends TestCase
{
    /**
     * @expectedException RuntimeException
     */
    public function testInstantiableFail()
    {
        $printerserver = new Network('127.0.0.0', 9100);
        $this->assertInstanceOf(Network::class, $printerserver);
    }
}
