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
    public function defaultModel($model = null);
    public function defaultCodePage($codepage = null);
    public function defaultRegionPage($region = null);
    public function defaultFont($font = null);
    //SET UP
    public function setCodePage($codepage = null);
    public function setRegionPage($region = null);
    public function setFont($font = null);
    public function setBold();
    public function setUnderlined();
    public function setItalic();
    public function setCondensed();
    public function setExpanded();
    public function setAlign($align = null);
    public function setReverseColors();
    public function setRotate90();
    public function setSpacing($horizontal = 0, $vertical = 0);
    public function setCharSpacing($value = 3);
    public function setParagraph($value = 0);
    public function setPrintMode($mode = null);
    //ACTIONS
    public function getBuffer($type = '');
    public function initialize();
    public function text($text = '');
    public function lineFeed($lines = 1);
    public function dotFeed($dots = 1);
    public function putImage();
    public function pulse($pin = 0, $on_ms = 120, $off_ms = 240);
    public function cut($mode = 'PARTIAL', $lines = 3);
    public function barcode(
        $data = '123456',
        $type = 'CODE128',
        $height = 162,
        $lineWidth = 2,
        $txtPosition = 'none',
        $txtFont = ''
    );
    public function barcodeQRCode($data = '', $level = 'L', $modelo = 2, $wmod = 4);
    public function barcodePDF417($data = '', $ecc = 5, $pheight = 2, $pwidth = 2, $colunms = 3);
    public function send();
    public function close();
}
