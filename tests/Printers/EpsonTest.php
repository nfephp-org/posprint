<?php

namespace Posprint\Tests\Printers;

/**
 * Unit Tests for Epson Class
 *
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

use Posprint\Printers\Epson;
use PHPUnit\Framework\TestCase;

class EpsonTest extends TestCase
{
    public function testInstantiable()
    {
        $printer = new Epson();
        $this->assertInstanceOf(Epson::class, $printer);
    }
    
    /**
     * @depends testInstantiable
     * @covers Posprint\Printers\DefaultPrinter::defaultRegionPage
     */
    public function testDefaultRegionPage()
    {
        $printer = new Epson();
        $actual = $printer->defaultRegionPage();
        $expected = 'LATIN';
        $this->assertEquals($expected, $actual);
        
        $expected = 'GERMANY';
        $actual = $printer->defaultRegionPage($expected);
        $this->assertEquals($expected, $actual);
        
        $expected = 'GERMANY';
        $actual = $printer->defaultRegionPage('NONEXISTS');
        $this->assertEquals($expected, $actual);
        
        $actual = $printer->defaultRegionPage('ALL');
        $expected = [
            'USA',
            'FRANCE',
            'GERMANY',
            'UK',
            'DENMARK',
            'SWEDEN',
            'ITALY',
            'SPAIN',
            'JAPAN',
            'NORWAY',
            'DENMARK2',
            'SPAIN2',
            'LATIN',
            'KOREA',
            'SLOVENIA',
            'CHINA',
            'VIETNAM',
            'ARABIA',
        ];
        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends testInstantiable
     * @covers Posprint\Printers\DefaultPrinter::defaultCodePage
     */
    public function testDefaultCodePage()
    {
        $printer = new Epson();
        $actual = $printer->defaultCodePage();
        $expected = 'CP437';
        $this->assertEquals($expected, $actual);
        
        $expected = 'CP850';
        $actual = $printer->defaultCodePage($expected);
        $this->assertEquals($expected, $actual);

        $actual = $printer->defaultCodePage('NONEXISTS');
        $this->assertEquals($expected, $actual);
        
        $expected = [
            'CP437',
            'CP850',
            'CP860',
            'CP863',
            'CP865',
            'CP851',
            'CP853',
            'CP857',
            'CP737',
            'ISO8859-7',
            'CP866',
            'CP852',
            'CP858',
            'CP720',
            'CP855',
            'CP861',
            'CP862',
            'CP864',
            'CP869',
            'ISO8859-2',
            'ISO8859-15',
            'WINDOWS-1250',
            'WINDOWS-1251',
            'WINDOWS-1252',
            'WINDOWS-1254',
            'WINDOWS-1255',
            'WINDOWS-1256',
            'WINDOWS-1257',
            'WINDOWS-1258'
        ];
        $actual = $printer->defaultCodePage('ALL');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends testInstantiable
     * @covers Posprint\Printers\DefaultPrinter::defaultFont
     */
  public function testDefaultFont()
    {
        $expected = 'A';
        $printer = new Epson();
        $actual = $printer->defaultFont();
        $this->assertEquals($expected, $actual);
        
        $expected = 'B';
        $actual = $printer->defaultFont($expected);
        $this->assertEquals($expected, $actual);
        
        $expected = 'SA';
        $actual = $printer->defaultFont($expected);
        $this->assertEquals($expected, $actual);
        
        $actual = $printer->defaultFont('NON');
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @depends testInstantiable
     * @covers Posprint\Printers\DefaultPrinter::defaultFont
     */
    public function testListAvaiableFonts()
    {
        $printer = new Epson();
        $actual = $printer->defaultFont('ALL');
        $expected = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 97 => 'SA', 98 => 'SB');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends testInstantiable
     * @covers Posprint\Printers\DefaultPrinter::initialize
     */
    public function testInitialize()
    {
        $expected = ' [ESC] @';
        $printer = new Epson();
        $printer->initialize();
        $actual = $printer->getBuffer();
        //$this->assertEquals($expected, $actual);
    }
    
    public function testSetCodePage()
    {
        $expected = chr(27)."@".chr(27)."t".chr(2);
        $printer = new Epson();
        $printer->initialize();
        $printer->setCodePage('CP850');
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testSetRegionPage()
    {
        $expected = chr(27)."@".chr(27)."R".chr(14);
        $printer = new Epson();
        $printer->initialize();
        $printer->setRegionPage('SLOVENIA');
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testSetFont()
    {
        $expected = chr(27)."@".chr(27)."M".chr(97);
        $printer = new Epson();
        $printer->initialize();
        $printer->setFont('SA');
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testSetBold()
    {
        $expected = chr(27)."@".chr(27)."E".chr(1);
        $printer = new Epson();
        $printer->initialize();
        $printer->setBold();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
        
        $expected = chr(27)."@".chr(27)."E".chr(1).chr(27)."E".chr(0);
        $printer->setBold();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testSetUnderlined()
    {
        $expected = chr(27)."@".chr(27)."-".chr(1);
        $printer = new Epson();
        $printer->initialize();
        $printer->setUnderlined();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
        
        $expected = chr(27)."@".chr(27)."-".chr(1).chr(27)."-".chr(0);
        $printer->setUnderlined();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testSetItalic()
    {
        //Epson dont have italic mode
        $this->assertTrue(true);
    }
    
    public function testSetAlign()
    {
        $expected = chr(27)."@".chr(27)."a".chr(1);
        $printer = new Epson();
        $printer->initialize();
        $printer->setAlign('C');
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
        
        $expected = chr(27)."@".chr(27)."a".chr(1).chr(27)."a".chr(0);
        $printer->setAlign('L');
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
        
        $expected = chr(27)."@".chr(27)."a".chr(1).chr(27)."a".chr(0).chr(27)."a".chr(0);
        $printer->setAlign('X');
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testSetReverseColors()
    {
        $expected = chr(27)."@".chr(29)."B".chr(1);
        $printer = new Epson();
        $printer->initialize();
        $printer->setReverseColors();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
        
        $expected = chr(27)."@".chr(29)."B".chr(1).chr(29)."B".chr(0);
        $printer->setReverseColors();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testSetExpanded()
    {
        $expected = chr(27)."@".chr(27)."!".chr(51);
        $printer = new Epson();
        $printer->initialize();
        $printer->setExpanded(4);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }

    public function testSetCondensed()
    {
        $this->assertTrue(true);
    }
    
    public function testSetPrintMode()
    {
        $this->assertTrue(true);
    }
    
    public function testSetRotate90()
    {
        $expected = chr(27)."@".chr(27)."V".chr(1);
        $printer = new Epson();
        $printer->initialize();
        $printer->setRotate90();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
        
        $expected = chr(27)."@".chr(27)."V".chr(1).chr(27)."V".chr(0);
        $printer->setRotate90();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testText()
    {
        $texto = 'Isso é um teste';
        $expected = chr(27)."@".chr(27)."t".chr(0).iconv('UTF-8', '437', $texto);
        $printer = new Epson();
        $printer->initialize();
        $printer->setCodePage('CP437');
        $printer->text($texto);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }

    public function testSetSpacing()
    {
        $expected = chr(27)."@".chr(29)."P".chr(100).chr(100);
        $printer = new Epson();
        $printer->initialize();
        $printer->setSpacing(100, 100);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testSetCharSpacing()
    {
        $expected = chr(27)."@".chr(27)." ".chr(100);
        $printer = new Epson();
        $printer->initialize();
        $printer->setCharSpacing(100);
        $actual = $printer->getBuffer('binS');
       // $this->assertEquals($expected, $actual);
    }
    
    public function testSetParagraph()
    {
        $expected = chr(27)."@".chr(27)."2";
        $printer = new Epson();
        $printer->initialize();
        $printer->setParagraph(0);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);

        $expected = chr(27)."@".chr(27)."2".chr(27)."3".chr(50);
        $printer->setParagraph(50);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testLineFeed()
    {
        $expected = chr(27)."@"."\n";
        $printer = new Epson();
        $printer->initialize();
        $printer->lineFeed();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);

        $expected = chr(27)."@"."\n".chr(27)."d".chr(5);
        $printer->lineFeed(5);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testDotFeed()
    {
        $expected = chr(27)."@".chr(27)."J".chr(10);
        $printer = new Epson();
        $printer->initialize();
        $printer->dotFeed(10);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
        
        $expected = chr(27)."@".chr(27)."J".chr(10).chr(27)."J".chr(0);
        $printer->dotFeed(100);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testPulse()
    {
        $expected = chr(27)."@".chr(27)."p".chr(48).chr(60).chr(120);
        $printer = new Epson();
        $printer->initialize();
        $printer->pulse();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);

        $expected = chr(27)."@".chr(27)."p".chr(48).chr(60).chr(120).chr(27)."p".chr(49).chr(100).chr(200);
        $printer->pulse(1, 200, 400);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testCut()
    {
        $expected = chr(27)."@".chr(29)."V".chr(65).chr(2);
        $printer = new Epson();
        $printer->initialize();
        $printer->cut('FULL', 2);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
        
        $expected = chr(27)."@".chr(29)."V".chr(65).chr(2).chr(29)."V".chr(66).chr(3);
        $printer->cut();
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testBarcode()
    {
        $data = '123456789ABCDEFDGHIJKLmnopq';
        $nlen = strlen($data);
        $expected = chr(27)."@".chr(29)."h".chr(200).chr(29)."w".chr(3).chr(29)."H".chr(0).chr(29)."f".chr(0).chr(29)."k".chr(73).chr($nlen).$data;
        $printer = new Epson();
        $printer->initialize();
        $printer->barcode($data, 'CODE128', 200, 3, 'none', '');
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
        
    }
    
    public function testBarcodeQRCode()
    {
        $data = "ESTOU AQUI ... ALO MUNDO";
        $len = strlen($data) + 3;
        $pH = ($len / 256);
        $pL = $len % 256;
        $expected = chr(27)."@";
        $expected .= chr(29)."(k".chr(4).chr(0).chr(49).chr(65).chr(50).chr(0);
        $expected .= chr(29)."(k".chr(3).chr(0).chr(49).chr(67).chr(4);
        $expected .= chr(29)."(k".chr(3).chr(0).chr(49).chr(69).chr(49);
        $expected .= chr(29)."(k".chr($pL).chr($pH).chr(49).chr(80).chr(48).$data;
        $expected .= chr(29)."(k".chr(3).chr(0).chr(49).chr(81).chr(48);
        $printer = new Epson();
        $printer->initialize();
        $printer->barcodeQRCode($data, 'M', 2, 4);
        $actual = $printer->getBuffer('binS');
        //$this->assertEquals($expected, $actual);
    }
    
    public function testPutImage()
    {
        $filename = realpath(dirname(__FILE__).'/../fixtures/image.bin');
        $imagebin = file_get_contents($filename);
        $expected = chr(27)."@".chr(29)."(L".$imagebin;
        $filename = realpath(dirname(__FILE__).'/../fixtures/tux.png');
        $printer = new Epson();
        $printer->initialize();
        $printer->putImage($filename);
        $actual = $printer->getBuffer('binS');
    }
}
