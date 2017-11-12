<?php
namespace Posprint\Tests\Extras;

/**
 * Unit Tests for PhpSerial Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Extras\PhpSerial;
use PHPUnit\Framework\TestCase;

class PhpSerialTest extends TestCase
{
    public function testInstantiable()
    {
        $connector = new PhpSerial();
        $this->assertInstanceOf(PhpSerial::class, $connector);
    }
    
    public function testGetBaudRate()
    {
        $connector = new PhpSerial();
        $boud = $connector->getBaudRate();
        $this->assertEquals($boud, 9600);
    }
    
    public function testSetBaudRate()
    {
        $connector = new PhpSerial();
        $resp = $connector->setBaudRate(115200);
        $boud = $connector->getBaudRate();
        $this->assertEquals($boud, 115200);
    }
    
    public function testGetFlowControl()
    {
        $connector = new PhpSerial();
        $flow = $connector->getFlowControl();
        $this->assertEquals($flow, 'none');
    }
    
    public function testSetFlowControl()
    {
        $connector = new PhpSerial();
        $resp = $connector->setFlowControl('xon/xoff');
        $flow = $connector->getFlowControl();
        $this->assertEquals($flow, 'xon/xoff');
    }
    
    public function testGetParity()
    {
        $connector = new PhpSerial();
        $parity = $connector->getParity();
        $this->assertEquals($parity, 'none');
    }
    
    public function testSetPatrity()
    {
        $connector = new PhpSerial();
        $resp = $connector->setParity('odd');
        $parity = $connector->getParity();
        $this->assertEquals($parity, 'odd');
    }
    
    public function testGetStopBits()
    {
        $connector = new PhpSerial();
        $stopbits = $connector->getStopBits();
        $this->assertEquals($stopbits, 1);
    }
    
    public function testSetStopBits()
    {
        $connector = new PhpSerial();
        $resp = $connector->setStopBits(2);
        $stopbits = $connector->getStopBits();
        $this->assertEquals($stopbits, 2);
    }
    
    public function testGetDataBits()
    {
        $connector = new PhpSerial();
        $charlength = $connector->getDataBits();
        $this->assertEquals($charlength, 8);
    }
    
    public function testSetDataBits()
    {
        $connector = new PhpSerial();
        $resp = $connector->setDataBits(7);
        $charlength = $connector->getDataBits();
        $this->assertEquals($charlength, 7);
    }
    
    public function testGetPort()
    {
        $connector = new PhpSerial();
        $device = $connector->getPort();
        $this->assertEquals($device, '/dev/ttyS0');
    }
    
    /**
     * @expectedException RunTimeException
     */
    public function testSetUpFail()
    {
        $connector = new PhpSerial();
        $resp = $connector->setUp();
    }
    
    /**
     * @expectedException RunTimeException
     * @expectedExceptionMessage Fail to open device. Check permissions.
     */
    public function testOpenFail()
    {
        $connector = new PhpSerial();
        $resp = $connector->open();
    }
    
    public function testOpen()
    {
        $ttyPath = realpath(dirname(__FILE__).'/../fixtures/ttyMock');
        $connector = new PhpSerial();
        $connector->setPort($ttyPath);
        $device = $connector->getDevice();
        $this->assertEquals($ttyPath, $device);
        $resp = $connector->open();
    }
}
