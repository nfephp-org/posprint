<?php
/**
 * Class SerialTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */

use Posprint\Connectors\Serial;

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
        $args = array('/dev/ttyS0',9600,8,'none');
        $this->phpserial = $this->getMockBuilder('PhpSerial')
                ->setConstructorArgs($args)
                ->setMethods(array('deviceSet','confBaudRate','confCharacterLength','confParity','deviceOpen'))
                ->getMock();
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
