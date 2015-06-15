<?php
namespace Posprint\Printers;

/**
 * Classe ZebraEpl das impressoras termicas.
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Printers\Basic\Printer;
use Posprint\Printers\Basic\PrinterInterface;
use Posprint\Common\Graphics;

class ZebraEpl extends Printer implements PrinterInterface
{
    public function setPaperWidth($width = 80)
    {
    }
    public function setMargins($left = 0, $right = 0)
    {
    }
    public function setSpacing($horizontal = 30, $vertical = 30)
    {
    }
    public function setCharSpacing($value = 3)
    {
    }
    public function setParagraf($paragrafo = 0)
    {
    }
    public function setPrintMode()
    {
    }
    public function setFont($font = 'A')
    {
    }
    public function setCharset()
    {
    }
    public function setInternational()
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
    public function setRotate90()
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
    public function feed()
    {
    }
    public function feedReverse()
    {
    }
    public function pulse()
    {
    }
    public function cut()
    {
    }
    public function barcodeEAN13()
    {
    }
    public function barcodeEAN8()
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
    public function putImage()
    {
    }
}