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
    public function send()
    {
        $msg = 'I8,A
q747
S2
O
JF
WN
ZT
Q464,25
N
A730,381,2,1,1,1,N,"Peca"
A730,205,2,1,1,1,N,"Produto"
A730,445,2,1,1,1,N,"Local"
A187,117,2,1,1,1,N,"Data Fabricacao"
A730,325,2,1,1,1,N,"Lote"
A730,85,2,1,1,1,N,"Peso Liquido"
A514,85,2,1,1,1,N,"Peso Bruto"
A730,261,2,1,1,1,N,"Referencia"
A730,149,2,1,1,1,N,"Cor"
A338,85,2,1,1,1,N,"Metragem"
A187,61,2,1,1,1,N,"Data Validade"
A730,21,2,2,1,1,N,"FIMATEC TEXTIL LTDA"
B466,447,2,3,2,5,102,N,"12345"
A356,339,2,3,1,1,N,"12345"
A730,427,2,4,1,1,N,"PH12"
A730,363,2,4,1,1,N,"12345"
A730,307,2,4,1,1,N,"222"
A187,99,2,4,1,1,N,"11/11/2011"
A730,243,2,4,1,1,N,"2324BCB00"
A730,187,2,4,1,1,N,"TECIDO TINTO"
A730,131,2,4,1,1,N,"BRANCO"
A730,67,2,4,1,1,N,"15,2KG"
A514,67,2,4,1,1,N,"16,1KG"
A338,67,2,4,1,1,N,"500MT"
A187,43,2,4,1,1,N,"11/11/2014"
P1
';
        $this->text($msg);
        
    }
    
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