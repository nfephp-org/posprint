<?php

namespace Posprint\Printers;

use Posprint\Printers\Basic\Basic;
use Posprint\Printers\PrinterInterface;

abstract class Printer extends Basic
{
    //métodos abstratos
    abstract public function barcodeEAN13();
    abstract public function barcodeEAN8();
    abstract public function barcode25();
    abstract public function barcode39();
    abstract public function barcode93();
    abstract public function barcode128();
    abstract public function barcodeQRCode();
    abstract public function barcodePdf417();
    abstract public function putImage();
}
