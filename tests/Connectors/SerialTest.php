<?php
/**
 * Class SerialTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */

use Posprint\Connectors\Serial;
use PhpSerial;

class SerialTest extends PHPUnit_Framework_TestCase
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
        $this->phpserial = $this->getMockBuilder('PhpSerial')->getMock();
        //Config device params in constructor
        $this->phpserial->expects($this->once());
        $this->phpserial->method('deviceSet');
        $this->phpserial->with($this->equalTo(self::DEVICE));
        $this->phpserial->expects($this->once());
        $this->phpserial->method('confBaudRate');
        $this->phpserial->with($this->equalTo(self::BAUD_RATE));
        $this->phpserial->expects($this->once());
        $this->phpserial->method('confCharacterLength');
        $this->phpserial->with($this->equalTo(self::BYTE_SIZE));
        $this->phpserial->expects($this->once());
        $this->phpserial->method('confParity');
        $this->phpserial->with($this->equalTo(self::PARITY));
        $this->phpserial->expects($this->once());
        $this->phpserial->method('deviceOpen');
        $this->object = new Serial(self::DEVICE, self::BAUD_RATE, self::BYTE_SIZE, self::PARITY);
    }
    
    public function testWrite()
    {
        $this->phpserial->expects($this->once());
        $this->phpserial->method('sendMessage');
        $this->phpserial->with($this->equalTo('testData'));
        $this->object->write('testData');
    }
    
    public function testClose()
    {
        $this->phpserial->expects($this->once());
        $this->phpserial->method('deviceClose');
        $this->object->close();
    }
}
