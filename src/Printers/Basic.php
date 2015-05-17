<?php

namespace Posprint\Printers;

use Posprint\Printers\Printer;
use Posprint\Connectors;
use Posprint\Common\Graphics;

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
    
    protected $connector;
    protected $characterTable = 0;
    
    public function __construct($connector = null)
    {
        if (isNull($connector)) {
            $this->connector = new Connectors\Buffer();
        }
    }
    
    public function setDevice()
    {
        
    }
    
    public function setMargins()
    {
        
    }

    public function setPrintMode($mode)
    {
        $this->connector->write(self::CTL_ESC . "!" . chr($mode));
    }
    
    public function setFont($font)
    {
        $this->connector->write(self::CTL_ESC . "M" . chr($font));
    }
    
    public function setCharset($table = 0)
    {
        $this->connector->write(self::CTL_ESC . "t" . chr($table));
        $this->characterTable = $table;
    }
    
    public function setBold($active = true)
    {
        $this->connector->write(self::CTL_ESC . "E". ($active ? chr(1) : chr(0)));
    }
    
    public function setItalic()
    {
        
    }
    
    public function setUnderlined($underline = 0)
    {
        $this->connector->write(self::ESC . "-". chr($underline));
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
    
    public function setJustification($justification)
    {
        $this->connector->write(self::CTL_ESC . "a" . chr($justification));
    }
    
    public function initialize()
    {
        $this->connector->write(self::CTL_ESC . "@");
        $this->characterTable = 0;
    }
        
    public function text($text = '')
    {
        $this->connector->write($text);
    }
    
    public function line()
    {
        $text = '----------------------------------------';
        $this->connector->write($text);
    }
    
    public function barcodeEAN13(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456789012'
    ) {
        $type = 2;
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
    
    public function barcodeEAN8(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '1234567'
    ) {
        $type = 3;
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
    
    public function barcodeUPCA(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456789012'
    ) {
        $type = 0;
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }

    public function barcodeUPCE(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456789012'
    ) {
        $type = 1;
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
    
    public function barcodeCODABAR(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        $type = 'none';
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
    
    public function barcodeMSI(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        $type = 'none';
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
    
    public function barcodeCODE11(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        $type = 'none';
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
    
    public function barcode25(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        $type = 'none';
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
    
    public function barcode39(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = 0,
        $txtFont = 0,
        $data = '123456'
    ) {
        $type = 4;
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
    
    public function barcode93(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        $type = 'none';
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
    
    public function barcode128(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        $type = 'none';
        $this->barcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
    
    protected function barcode(
        $type = 0,
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        if ($type != 'none') {
            $this->connector->write(self::CTL_GS . "h" . chr($height));
            $this->connector->write(self::CTL_GS . 'w' . chr($lineWidth));
            $this->connector->write(self::CTL_GS . 'H' . chr($txtPosition));
            $this->connector->write(self::CTL_GS . 'f' . chr($txtFont));
            $this->connector->write(self::CTL_GS . "k" . chr($type) . $data . self::NUL);
        }
    }


    public function barcodeQRCode()
    {
        
    }
    
    public function barcodePdf417()
    {
        
    }

    public function feed($lines = 1)
    {
        if ($lines <= 1) {
            $this->connector->write(self::CTL_LF);
        } else {
            $this->connector->write(self::CTL_ESC . "d" . chr($lines));
        }
    }
    
    public function reverseFeed($lines = 1)
    {
        $this->connector->write(self::CTL_ESC . "e" . chr($lines));
    }
    
    public function pulse($pin = 0, $onMs = 120, $offMs = 240)
    {
        $this->connector->write(self::CTL_ESC . "p" . chr($pin + 48) . chr($onMs / 2) . chr($offMs / 2));
    }
    
    public function putImage()
    {
        
    }
    
    public function cut($mode = 65, $lines = 3)
    {
        $this->connector->write(self::CTL_GS . "V" . chr($mode) . chr($lines));
    }
    
    public function send()
    {
        
    }
    
    public function close()
    {
        $this->connector->close();
    }
}
