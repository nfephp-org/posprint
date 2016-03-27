<?php

namespace Posprint\Tests\Graphics;

/**
 * Unit Tests for Graphics Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Graphics\Graphics;

class GraphicsTest extends \PHPUnit_Framework_TestCase
{
    
    public function testInstantiable()
    {
        $graphics = new Graphics();
        $this->assertInstanceOf(Graphics::class, $graphics);
    }
    
    public function testLoadImage()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
    }
    
    /**
     * @expectedException RunTimeException
     * @expectedExceptionMessage It is not possible to use or handle this type of image with GD
     */
    public function testLoaImageFailType()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.tiff');
        $graphics = new Graphics($imagePath);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Image file not found.
     */
    public function testLoaImageFailNotFind()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.svg');
        $graphics = new Graphics($imagePath);
    }
    
    public function testLoadImageBMP()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.bmp');
        $graphics = new Graphics($imagePath);
    }
    
    public function testConvert2BMP()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
        $result = $graphics->convert2BMP();
        $filename = realpath(dirname(__FILE__).'/../fixtures').DIRECTORY_SEPARATOR.'tux.bmp';
        $expected = file_get_contents($filename);
        $this->assertEquals($result, $expected);
    }
    
    public function testConvert2BMPSave()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
        $filename1 = realpath(dirname(__FILE__).'/../fixtures').DIRECTORY_SEPARATOR.'new.bmp';
        $graphics->convert2BMP($filename1);
        $result = file_get_contents($filename1);
        $filename2 = realpath(dirname(__FILE__).'/../fixtures').DIRECTORY_SEPARATOR.'tux.bmp';
        $expected = file_get_contents($filename2);
        $this->assertEquals($result, $expected);
        unlink($filename1);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cant open file /new.bmp. Check permissions.
     */
    public function testeConvert2BMPSaveFail()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
        $filename = realpath(dirname(__FILE__).'/../noexists').DIRECTORY_SEPARATOR.'new.bmp';
        $graphics->convert2BMP($filename);
    }
    
    public function testGetRaster()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
        $result = $graphics->getRasterImage();
        $filename = realpath(dirname(__FILE__).'/../fixtures').DIRECTORY_SEPARATOR.'tux.raw';
        $expected = file_get_contents($filename);
        //$this->assertEquals($result, $expected);
    }
    
    public function testQRCode()
    {
        $graphics = new Graphics();
        $graphics->imageQRCode();
        $filename = realpath(dirname(__FILE__).'/../fixtures').DIRECTORY_SEPARATOR.'qr.png';
        $graphics->save($filename);
    }
}
