<?php

namespace Posprint\Tests\Connectors;

/**
 * Unit Tests for Network connector Class
 * 
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Connectors\Network;

class NetworkTest extends \PHPUnit_Framework_TestCase
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