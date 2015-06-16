<?php

namespace PosprintTest\Connectors;

/**
 * Class SerialTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */

use Posprint\Connectors\Serial;

class SerialTest extends \PHPUnit_Framework_TestCase
{
    const DEVICE    = '/dev/ttyS0';
    const BAUD_RATE = 9600;
    const BYTE_SIZE = 8;
    const PARITY    = 'none';

    private $object;
    private $phpserial;

    protected function setUp()
    {
        parent::setUp();
        $this->phpserial = $this->getMockBuilder('PhpSerial')
            ->disableOriginalConstructor()
            ->getMock();
        $this->object = new Serial(self::DEVICE, self::BAUD_RATE, self::BYTE_SIZE, self::PARITY);
    }
    
    public function testWrite()
    {
        $this->object->write('testData');
        $this->assertTrue(true);
    }
    
    public function testClose()
    {
        $this->object->close();
        $this->assertTrue(true);
    }
}
