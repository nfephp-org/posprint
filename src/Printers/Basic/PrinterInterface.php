<?php

namespace Posprint\Printers\Basic;

/**
 * Interface com os comandos básicos das impressoras POS
 * 
 * IMPORTANTE: Alguns comandos podem não existir para uma determinada impressora
 * ou não funcionar devido a diferenças em seu firmware, mesmo entre impressoras
 * de mesmo modelo e marca. Portanto é importante garantir um firmware atualizado.
 * 
 * @category   API
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

interface PrinterInterface
{
    public function setPaperWidth();
    public function setMargins();
    public function setSpacing();
    public function setCharSpacing();
    public function setParagraf();
    public function setPrintMode();
    public function setFont();
    public function setCharset();
    public function setInternational();
    public function setBold();
    public function setItalic();
    public function setUnderlined();
    public function setExpanded();
    public function setCondensed();
    public function setRotate90();
    public function setReverseColors();
    public function setJustification();
    public function initialize();
    public function text();
    public function line();
    public function barcodeEAN13();
    public function barcodeEAN8();
    public function barcode25();
    public function barcode39();
    public function barcode93();
    public function barcode128();
    public function barcodeQRCode();
    public function barcodePdf417();

    public function feed();
    public function feedReverse();
    public function pulse();
    public function putImage();
    public function cut();
    public function send();
    public function close();
}
