<?php

namespace Posprint\Printers;

/**
 * Interface com os comandos básicos das impressoras POS
 * 
 * IMPORTANTE: Alguns comandos podem não existir para uma determinada impressora
 * ou não funcionar devido a diferenças em seu firmware, mesmo entre impressoras
 * de mesmo modelo e marca. Portanto é importante garantir um firmware atualizado.
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

interface PrinterInterface
{
    //SET UP
    public function defaultRegionPage();
    public function defaultCodePage();
    public function setCodePage();
    public function setRegionPage();
    public function defaultFont();
    public function setFont();
    public function setBold();
    public function setUnderlined();
    public function setAlign();
    public function setReverseColors();
    public function setRotate90();
    public function setExpanded();
    public function setCondensed();
    
    public function setSpacing();
    public function setCharSpacing();
    public function setParagraph();
    public function setPrintMode();
    
    
    
    
    //ACTIONS
    public function initialize();
    public function text();
    public function barcode();
    public function barcodeQRCode();
    
    public function lineFeed();
    public function dotFeed();
    public function pulse();
    public function putImage();
    public function cut();
    public function send();
    public function close();
}
