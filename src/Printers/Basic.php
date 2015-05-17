<?php

namespace Posprint\Printers;

use Posprint\Printers\Printer;

class Basic implements Printer
{
    const NUL = "\x00"; //NULL
    const CTL_ESC = "\x1b"; //ESC command
    const CTL_FS = "\x1c"; //FS command
    const CTL_GS = "\x1d"; //GS command
    const CTL_LF = "\x0a"; //line feed
    const CTL_FF = "\x0c"; //form feed
    const CTL_CR = "\x0d"; //carriage return
    const CTL_HT = "\x09"; //horizontal tab
    const CTL_VT = "\x0b";
    const CTL_DLE = "\x10";
    const CTL_EOT = "\x4";
    const CTL_CAN = "\x18"; //cancela
    
    public function __construct()
    {
        
    }
    
    public function setDevice()
    {
        
    }
    
    public function setMargins()
    {
        
    }
    
    public function setFontMode()
    {
        
    }
    
    public function setCharset()
    {
        
    }
    
    public function setBold()
    {
        
    }
    
    public function setItalic()
    {
        
    }
    
    public function setUnderlined()
    {
        
    }
    
    public function setExpanded()
    {
        
    }
    
    public function setCondensed()
    {
        
    }
    
    public function setParagraf()
    {
        
    }
    
    public function setReverseColors()
    {
        
    }
    
    public function setJustification()
    {
        
    }
    
    public function initialize()
    {
        
    }
        
    public function text()
    {
        
    }
    
    public function line()
    {
        
    }
    
    public function barcodeEAN13()
    {
        
    }
    
    public function barcodeEAN8()
    {
        
    }
    
    public function barcodeUPCA()
    {
        
    }
    
    public function barcodeCODABAR()
    {
        
    }
    
    public function barcodeMSI()
    {
        
    }
    
    public function barcodeCODE11()
    {
        
    }
    
    public function barcode25()
    {
        
    }
    
    public function barcode39()
    {
        
    }
    
    public function barcode93()
    {
        
    }
    
    public function barcode128()
    {
        
    }
    
    public function barcodeQRCode()
    {
        
    }
    
    public function barcodePdf417()
    {
        
    }

    public function feed()
    {
        
    }
    
    public function reverseFeed()
    {
        
    }
    
    public function pulse()
    {
        
    }
    
    public function putImage()
    {
        
    }
    
    public function cut()
    {
        
    }
    
    public function send()
    {
        
    }
    
    public function close()
    {
        
    }
}
