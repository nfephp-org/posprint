<?php

namespace Posprint\Tests\Connectors;

/**
 * Unit Tests for Buffer connector Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Connectors\Buffer;
use PHPUnit\Framework\TestCase;

class BufferTest extends TestCase
{
    /**
     * @covers Posprint\Connectors\Buffer::__construct
     */
    public function testInstantiable()
    {
        $buffer = new Buffer();
        $this->assertInstanceOf(Buffer::class, $buffer);
    }
    
    /**
     * @depends testInstantiable
     * @covers Posprint\Connectors\Buffer::write
     * @covers Posprint\Connectors\Buffer::getDataBinary
     */
    public function testWrite()
    {
        $buffer = new Buffer();
        $data = "1234567890ASDFG";
        $buffer->write($data);
        $response = $buffer->getDataBinary(true);
        $expected = array($data);
        $this->assertEquals($response, $expected);
    }
    
    /**
     * @depends testInstantiable
     * @covers Posprint\Connectors\Buffer::write
     * @covers Posprint\Connectors\Buffer::getDataJson
     */
    public function testGetDataJson()
    {
        $buffer = new Buffer();
        $data = "1234567890ASDFG";
        $buffer->write($data);
        $response = $buffer->getDataJson(true);
        $expected = json_encode(array($data));
        $this->assertEquals($response, $expected);
    }
    
    /**
     * @depends testInstantiable
     * @covers Posprint\Connectors\Buffer::write
     * @covers Posprint\Connectors\Buffer::getDataBase64
     */
    public function testGetDataBase64()
    {
        $buffer = new Buffer();
        $data = "1234567890ASDFG";
        $buffer->write($data);
        $response = $buffer->getDataBase64(false);
        $expected = base64_encode($data);
        $this->assertEquals($response, $expected);
    }

  /**
     * @depends testInstantiable
     * @covers Posprint\Connectors\Buffer::write
     * @covers Posprint\Connectors\Buffer::getDataBase64
     */
    public function testGetDataBase64Array()
    {
        $buffer = new Buffer();
        $data = "1234567890ASDFG";
        $buffer->write($data);
        $response = $buffer->getDataBase64(true);
        $expected = array(base64_encode($data));
        $this->assertEquals($response, $expected);
    }
    
    /**
     * @depends testInstantiable
     * @covers Posprint\Connectors\Buffer::write
     * @covers Posprint\Connectors\Buffer::getDataReadable
     * @covers Posprint\Connectors\Buffer::friendlyBinary
     */
    public function testGetDataReadable()
    {
        $buffer = new Buffer();
        $data = "123".chr(10)."45678".chr(8)."90A".chr(0)."SDFG";
        $buffer->write($data);
        $response = $buffer->getDataReadable(false);
        $expected = '123 [LF] 45678 (08h) 90A [NUL] SDFG';
        $this->assertEquals($response, $expected);
    }

    /**
     * @depends testInstantiable
     * @covers Posprint\Connectors\Buffer::close
     */
    public function testClose()
    {
        $buffer = new Buffer();
        $data = "123".chr(10)."45678".chr(8)."90A".chr(0)."SDFG";
        $buffer->write($data);
        $buffer->close();
        $response = $buffer->read(20);
        $expected = '';
        $this->assertEquals($response, $expected);
    }
    
    /**
     * @depends testInstantiable
     * @covers Posprint\Connectors\Buffer::write
     * @covers Posprint\Connectors\Buffer::read
     * @covers Posprint\Connectors\Buffer::getDataReadable
     * @covers Posprint\Connectors\Buffer::friendlyBinary
     * @covers Posprint\Connectors\Buffer::close
     */
    public function testRead()
    {
        $buffer = new Buffer();
        $data = "123".chr(10)."45678".chr(8)."90A".chr(0)."SDFG";
        $buffer->write($data);
        $response = $buffer->read(20);
        $expected = '123 [LF] 45678 (08h) 90A [NUL] SDFG';
        $this->assertEquals($response, $expected);
    }
}
