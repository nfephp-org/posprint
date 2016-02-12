<?php

namespace Posprint\Printers\Basic;

/**
 * Abstract Class Printer extends Basic class
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Printers\Basic\Basic;

abstract class Printer extends Basic
{
    //abstract methods
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
