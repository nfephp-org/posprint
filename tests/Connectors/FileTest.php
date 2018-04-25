<?php

namespace Posprint\Tests\Connectors;

/**
 * Unit Tests for File connector Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Connectors\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testInstantiable()
    {
        $filePath = realpath(dirname(__FILE__).'/../fixtures/escpos.prn');
        $file = new File($filePath);
        $this->assertInstanceOf(File::class, $file);
    }
    
    /**
     * @depends testInstantiable
     */
    public function testInstantiableFailsWithoutFilePath()
    {
        $file = new File();
        $this->assertInstanceOf(File::class, $file);
    }
    
    /**
     * @depends testInstantiable
     * @expectedException RuntimeException
     * @expectedExceptionMessage Failed to open the file. Check the permissions!
     */
    public function testInstantiableFailsWithoutPermissions()
    {
        $filePath = '/var/fixtures/escpos.prn';
        $file = new File($filePath);
        $this->assertInstanceOf(File::class, $file);
    }

    /**
     * @depends testInstantiable
     */
    public function testWriteNothing()
    {
        $filePath = realpath(dirname(__FILE__).'/../fixtures/escpos.prn');
        $file = new File($filePath);
        $response = $file->write("");
        $expected = 0;
        $this->assertEquals($response, $expected);
    }
    
    /**
     * @depends testInstantiable
     */
    public function testWrite()
    {
        $filePath = realpath(dirname(__FILE__).'/../fixtures/escpos.prn');
        $file = new File($filePath);
        $data = "1234567890ASDFG".chr(10).chr(27)."qqqqq";
        $response = $file->write($data);
        $expected = strlen($data);
        $this->assertEquals($response, $expected);
    }

    /**
     * @depends testInstantiable
     */
    public function testClose()
    {
        $filePath = realpath(dirname(__FILE__).'/../fixtures/escpos.prn');
        $file = new File($filePath);
        $file->close();
        $this->assertTrue(true);
    }
    
    /**
     * @depends testInstantiable
     */
    public function testRead()
    {
        $filePath = realpath(dirname(__FILE__).'/../fixtures/escpos.prn');
        $file = new File($filePath);
        $response = $file->read(10);
        $expected = '1234567890';
        $this->assertEquals($response, $expected);
    }
    
    /**
     * @depends testInstantiable
     */
    public function testReadFull()
    {
        $filePath = realpath(dirname(__FILE__).'/../fixtures/escpos.prn');
        $file = new File($filePath);
        $response = $file->read();
        $expected = "1234567890ASDFG".chr(10).chr(27)."qqqqq";
        $this->assertEquals($response, $expected);
    }
}
