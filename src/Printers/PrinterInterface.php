<?php

namespace Posprint\Printers;

/**
 * Interface with the basic commands of POS printers
 *
 * IMPORTANT: Some commands may not exist for a particular printer
 * Or may not work due to differences in their firmware, even among printers
 * of the same model and brand.
 * Therefore it is important to ensure an updated firmware.
 *
 * @category  NFePHP
 * @package   Posprint
 * @copyright Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/posprint for the canonical source repository
 */

interface PrinterInterface
{
    //DEFAULT ENVIRONMENT
    public function defaultModel();
    public function defaultCodePage();
    public function defaultRegionPage();
    public function defaultFont();
    //SET UP
    public function setCodePage();
    public function setRegionPage();
    public function setFont();
    public function setBold();
    public function setUnderlined();
    public function setItalic();
    public function setCondensed();
    public function setExpanded();
    public function setAlign();
    public function setReverseColors();
    public function setRotate90();
    public function setSpacing();
    public function setCharSpacing();
    public function setParagraph();
    public function setPrintMode();
    //ACTIONS
    public function initialize();
    public function text();
    public function lineFeed();
    public function dotFeed();
    public function putImage();
    public function pulse();
    public function cut();
    public function barcode();
    public function barcodeQRCode();
    public function barcodePDF417();
    public function send();
    public function close();
}
