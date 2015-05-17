<?php

namespace Posprint\Printers;

interface Printer
{
    public function setDevice();
    public function setMargins();
    public function setFontMode();
    public function setCharset();
    public function setBold();
    public function setItalic();
    public function setUnderlined();
    public function setExpanded();
    public function setCondensed();
    public function setParagraf();
    public function setReverseColors();
    public function setJustification();
    public function initialize();
    
    public function text();
    public function line();
    public function barcodeEAN13();
    public function barcodeEAN8();
    public function barcodeUPCA();
    public function barcodeCODABAR();
    public function barcodeMSI();
    public function barcodeCODE11();
    public function barcode25();
    public function barcode39();
    public function barcode93();
    public function barcode128();
    public function barcodeQRCode();
    public function barcodePdf417();

    public function feed();
    public function reverseFeed();
    public function pulse();
    public function putImage();
    public function cut();
    public function send();
    public function close();
}
