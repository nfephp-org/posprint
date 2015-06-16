<?php

namespace PosprintTest\Common;

/**
 * Class GraphicsTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */

use Posprint\Common\Graphics;


class GraphicsTest extends \PHPUnit_Framework_TestCase
{
    protected $folderBase;
    protected $graf;
    
    protected function setUp()
    {
        parent::setUp();
        $this->folderBase = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR;
        $this->graf = new Graphics();
    }
    
    public function testgetImageQRCode()
    {
        $msg = 'https://www.sefaz.rs.gov.br/NFCE/NFCE-COM.aspx?chNFe=43141006354976000149650540000086781171025455&nVersao=100&tpAmb=2&dhEmi=323031342d31302d33305431353a33303a32302d30323a3030&vNF=0.10&vICMS=0.00&digVal=682f4d6b6b366134416d6f7434346d335a386947354f354b6e50453d&cIdToken=000001&cHashQRCode=771A7CE8C50D01101BDB325611F582B67FFF36D0';
        $this->graf->getImageQRCode($msg, 200, 1, 'low');
        $raster = $this->graf->getImageBinary();
        $fixture = file_get_contents($this->folderBase.'qrdemo.dat');
        $this->assertEquals($raster, $fixture);
    }
    
    public function testLoadImg()
    {
        $filename = $this->folderBase.'tux.jpg';
        $this->graf->loadImage($filename);
        $wdt = $this->graf->getWidth();
        $this->assertEquals($wdt, 196);
    }

    public function testGetImageBinary()
    {
        $filename = $this->folderBase.'tux.jpg';
        $this->graf->loadImage($filename, 208);
        $wdt = $this->graf->getWidth();
        $this->assertEquals($wdt, 208);
    }
    
    public function testResizeImage()
    {
        $filename = $this->folderBase.'tux.jpg';
        $this->graf->loadImage($filename, 208, 229);
        $wdt = $this->graf->getWidth();
        $hgt = $this->graf->getHeight();
        $this->assertEquals($wdt, 208);
        $this->assertEquals($hgt, 229);
    }
    
    public function testGetImage()
    {
        $this->assertTrue(true);
    }
}
