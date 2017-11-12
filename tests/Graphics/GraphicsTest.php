<?php

namespace Posprint\Tests\Graphics;

/**
 * Unit Tests for Graphics Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Graphics\Graphics;
use PHPUnit\Framework\TestCase;

class GraphicsTest extends TestCase
{
    
    public function testInstantiable()
    {
        $graphics = new Graphics();
        $this->assertInstanceOf(Graphics::class, $graphics);
    }
    
    /**
     * @depends testInstantiable
     */
    public function testLoadImage()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
    }

    /**
     * @depends testInstantiable
     */
    public function testGetDimImageWithNoImage()
    {
        $graphics = new Graphics();
        $result = $graphics->getDimImage();
        $expected = ['height'  => 0, 'width' => 0];
        $this->assertEquals($expected, $result);
    }

    /**
     * @depends testInstantiable
     */
    public function testGetHeightBytes()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
        $result = $graphics->getHeightBytes();
        $expected = 40;
        $this->assertEquals($expected, $result);
    }
    
    
    /**
     * @depends testInstantiable
     * @expectedException RunTimeException
     * @expectedExceptionMessage It is not possible to use or handle this type of image with GD
     */
    public function testLoaImageFailType()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.tiff');
        $graphics = new Graphics($imagePath);
    }
    
    /**
     * @depends testInstantiable
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Image file not found.
     */
    public function testLoaImageFailNotFind()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.svg');
        $graphics = new Graphics($imagePath);
    }

    /**
     * @depends testInstantiable
     * @covers Posprint\Graphics\Graphics::loadBMP
     * @covers Posprint\Graphics\Graphics::save
     * @covers Posprint\Graphics\Graphics::saveImage
     */
    public function testLoadImageBMP()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.bmp');
        $graphics = new Graphics($imagePath);
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux2.png');
        $graphics->save($imagePath, 'PNG');
    }

    /**
     * @depends testInstantiable
     */
    public function testConvert2BMPSave()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
        $filename1 = realpath(dirname(__FILE__).'/../fixtures').DIRECTORY_SEPARATOR.'new.bmp';
        $graphics->save($filename1, 'BMP');
        $result = file_get_contents($filename1);
        $filename2 = realpath(dirname(__FILE__).'/../fixtures').DIRECTORY_SEPARATOR.'tux.bmp';
        $expected = file_get_contents($filename2);
        $this->assertEquals($result, $expected);
        unlink($filename1);
    }

    /**
     * @depends testInstantiable
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cant open file /new.bmp. Check permissions.
     */
    public function testeConvert2BMPSaveFail()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
        $filename = realpath(dirname(__FILE__).'/../noexists').DIRECTORY_SEPARATOR.'new.bmp';
        $graphics->save($filename, 'BMP');
    }

    /**
     * @depends testInstantiable
     * @covers Posprint\Graphics\Graphics::convertRaster
     * @covers Posprint\Graphics\Graphics::getRasterImage
     */
    public function testGetRaster()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
        $result = $graphics->getRasterImage();
        $filename = realpath(dirname(__FILE__).'/../fixtures').DIRECTORY_SEPARATOR.'tux.raw';
        $expected = file_get_contents($filename);
        $result = $graphics->getRasterImage();
        //$this->assertEquals($result, $expected);
    }

    /**
     * @depends testInstantiable
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage No dimensions was passed.
     */
    public function testResizeImageFail()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
        $graphics->resizeImage();
    }
    
    /**
     * @depends testInstantiable
     */
    public function testResizeImageFromHeigthOnly()
    {
        $imagePath = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $graphics = new Graphics($imagePath);
        $graphics->resizeImage(null, 264);
        $result = $graphics->getHeight();
        $expected = 264;
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @depends testInstantiable
     */
    public function testQRCode()
    {
        $graphics = new Graphics();
        $graphics->imageQRCode();
        $filename = realpath(dirname(__FILE__).'/../fixtures').DIRECTORY_SEPARATOR.'qr.png';
        $graphics->save($filename);
    }
}
