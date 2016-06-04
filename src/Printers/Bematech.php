<?php

namespace Posprint\Printers;

/**
 * Bematech class for POS printer
 * Model: MP 4200TH
 *
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Printers\DefaultPrinter;
use Posprint\Printers\PrinterInterface;

class Bematech extends DefaultPrinter implements PrinterInterface
{
    
    /**
     * List all available code pages.
     *
     * @var array
     */
    protected $aCodePage = array(
        'CP437' => array('conv' => '437', 'table' => '3', 'desc' => 'PC437: USA, Standard Europe'),
        'CP850' => array('conv' => '850', 'table' => '2', 'desc' => 'PC850: Multilingual'),
        'CP858' => array('conv' => '858', 'table' => '5', 'desc' => 'PC858: Multilingual'),
        'CP860' => array('conv' => '860', 'table' => '4', 'desc' => 'PC860: Portuguese'),
        'CP864' => array('conv' => '864', 'table' => '7', 'desc' => 'PC864: Arabic'),
        'CP866' => array('conv' => '866', 'table' => '6', 'desc' => 'PC866: Cyrillic'),
        'UTF8'  => array('conv' => 'UTF8', 'table' => '8', 'desc' => 'UTF-8: Unicode')
    );
    /**
     * List all available region pages.
     *
     * @var array
     */
    protected $aRegion = array(
        'LATIN'
    );
    /**
     * Seleted printer mode.
     *
     * @var string
     */
    protected $printerMode = 'ESCBEMA';
    /**
     * List all avaiable fonts
     *
     * @var array
     */
    protected $aFont = array(0 => 'C', 1 => 'D');
    /**
     * Selected internal font.
     *
     * @var string
     */
    protected $font = 'C';
    /**
     * Seleted code page
     * Defined in printer class.
     *
     * @var string
     */
    protected $codepage = 'CP850';
    /**
     * Acceptable barcodes list
     * @var array
     */
    protected $barcode1Dlist = [
        'UPC_A' => 65,
        'UPC_E' => 66,
        'EAN13' => 67,
        'EAN8' => 68,
        'CODE39' => 69,
        'I25' => 70,
        'CODABAR' => 71,
        'CODE93' => 72,
        'CODE128' => 73,
        'ISBN' => null,
        'MSI' => null
    ];
    /**
     * List of supported models
     * @var array
     */
    protected $modelList = [
        '4200TH'
    ];
    /**
     * Selected model
     * @var string
     */
    protected $printerModel = '4200TH';

    //public function __construct(); vide DefaultPrinter
    //public function defaultCodePage(); vide DefaultPrinter
    //public function defaultRegionPage(); vide DefaultPrinter
    //public function defaultFont(); vide DefaultPrinter
    //public function defaultModel(); vide DefaultPrinter
    //public function initialize(); vide DefaultPrinter
    
    /**
     * Select printer mode
     *
     * @param string $mode
     */
    public function setPrintMode($mode = 'ESCBEMA')
    {
        if ($mode != $this->printerMode) {
            switch ($mode) {
                case 'ESCBEMA':
                    $this->printerMode = 'ESCBEMA';
                    break;
                default:
                    $this->printerMode = 'ESCPOS';
            }
        }
        $nmod = 0;
        if ($this->printerMode == 'ESCPOS') {
            $nmod = 1;
        }
        $this->buffer->write(self::GS . chr(249) . chr(53) . $nmode);
    }
    
    /**
     * Set a codepage table in printer.
     *
     * @param string $codepage
     */
    public function setCodePage($codepage = null)
    {
        if ($this->printerMode == 'ESCPOS') {
            parent::setCodePage($codepage);
            return;
        }
        $codepage = $this->defaultCodePage($codepage);
        $this->buffer->write(self::GS . chr(249) . chr(55) . chr($this->charsetTableNum));
    }
    
    /**
     * Set a region page.
     * The numeric key of array $this->aRegion is the command parameter.
     *
     * @param string $region
     */
    public function setRegionPage($region = null)
    {
        //not used for this printer
    }
    
    /**
     * Set a printer font
     * If send a valid font name will set the printer otherelse a default font is selected
     *
     * @param string $font
     */
    public function setFont($font = null)
    {
        if ($this->printerMode == 'ESCPOS') {
            parent::setFont($codepage);
        }
    }

    /**
     * Set emphasys mode on or off.
     */
    public function setBold()
    {
        if ($this->printerMode == 'ESCPOS') {
            parent::setBold();
            return;
        }
        $this->boldMode = ! $this->boldMode;
        if ($this->boldMode) {
            $this->buffer->write(self::ESC . 'E');
        } else {
            $this->buffer->write(self::ESC . 'F');
        }
    }

    /**
     * Set Italic mode
     */
    public function setItalic()
    {
        $this->italicMode = ! $this->italicMode;
        if ($this->printerMode == 'ESCPOS') {
            return;
        }
        if ($this->italicMode) {
            $this->buffer->write(self::ESC . '4');
        } else {
            $this->buffer->write(self::ESC . '5');
        }
    }

    //public function setUnderlined(); vide DefaultPrinter

    /**
     * Set or unset condensed mode.
     */
    public function setCondensed()
    {
        if ($this->printerMode == 'ESCPOS') {
            return;
        }
        $this->condensedMode = ! $this->condensedMode;
        if ($this->condensedMode) {
            $this->buffer->write(self::SI);
        } else {
            $this->buffer->write(self::DC2);
        }
    }
    
    /**
     * Set or unset expanded mode.
     *
     * @param integer $size not used
     */
    public function setExpanded($size = null)
    {
        $this->expandedMode = ! $this->expandedMode;
        if ($this->printerMode == 'ESCPOS') {
            return;
        }
        $n = 0;
        if ($this->expandedMode) {
            $n = 1;
        }
        $this->buffer->write(self::ESC . 'W' . chr($n));
    }
    
    //public function setAlign(); vide DefaultPrinter
    
    /**
     * Turns white/black reverse print On or Off for characters.
     */
    public function setReverseColors()
    {
        if ($this->printerMode == 'ESCPOS') {
            parent::reverseColors();
        }
    }
    
    /**
     * Set rotate 90 degrees.
     */
    public function setRotate90()
    {
        if ($this->printerMode == 'ESCPOS') {
            parent::setRotate90();
        }
    }
    
    /**
     * Set horizontal and vertical motion units
     * $horizontal => character spacing 1/x"
     * $vertical => line spacing 1/y".
     */
    public function setSpacing($horizontal = 30, $vertical = 30)
    {
        //not used for this printer
    }
    
    /**
     * Set right-side character spacing
     * 0 ≤ n ≤ 255 => 1/x".
     *
     * @param int $value
     */
    public function setCharSpacing($value = 3)
    {
        //not used for this printer
    }
    
    //public function setParagraph(); vide DefaultPrinter
    //public function text(); vide default

    /**
     * Prints data and feeds paper n lines
     * ESC d n Prints data and feeds paper n lines.
     *
     * @param integer $lines
     */
    public function lineFeed($lines = 1)
    {
        if ($this->printerMode == 'ESCPOS') {
            parent::lineFeed($lines);
            return;
        }
        $lines = self::validateInteger($lines, 0, 255, 1);
        for ($lin = 1; $lin <= $lines; $lin++) {
            $this->buffer->write(self::LF);
        }
    }
    
    //public function dotFeed(); vide default

    /**
     * Put a image
     * GS v0 m xL xH yL yH d1 ... dk
     *
     * @param string $filename
     * @param intger $width
     * @param integer $height
     * @param integer $size resolution relation
     * @throws RuntimeException
     */
    public function putImage($filename = '', $width = null, $height = null, $size = 0)
    {
        try {
            $img = new Graphics($filename, $width, $height);
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage());
        }
        $size = self::validateInteger($size, 0, 3, 0);
        //get xL xH yL yH
        $imgHeader = self::dataHeader(array($img->getWidth(), $img->getHeight()), true);
        //send graphics command to printer
        $this->buffer->write(self::GS.'v0'.chr($size).$imgHeader.$img->getRasterImage());
    }

    /**
     * Generate a pulse, for opening a cash drawer if one is connected.
     *
     *
     * @param int $pin    0 or 1, for pin 2 or pin 5 kick-out connector respectively.
     * @param int $on_ms  pulse ON time, in milliseconds.
     * @param int $off_ms pulse OFF time, in milliseconds.
     */
    public function pulse($pin = 0, $on_ms = 120, $off_ms = 240)
    {
        if ($this->printerMode == 'ESCPOS') {
            parent::pulse($pin, $on_ms, $off_ms);
            return;
        }
        $on_ms = self::validateInteger($on_ms, 50, 250, 50);
        if ($pin == 0 || $pin == 1) {
            $this->buffer->write(self::ESC.'v'. chr($on_ms));
        } else {
            $this->buffer->write(self::ESC. chr(128) . chr($on_ms));
        }
    }

    /**
     * Cut the paper.
     *
     * @param int $mode  FULL or PARTIAL. If not specified, FULL will be used.
     * @param int $lines Number of lines to feed after cut
     */
    public function cut($mode = 'PARTIAL', $lines = 3)
    {
        if ($this->printerMode == 'ESCPOS') {
            parent::cut($mode, $lines);
        }
        $lines = self::validateInteger($lines, 1, 10, 3);
        if ($mode == 'FULL') {
            $this->buffer->write(self::ESC.'w');
        } else {
            $this->buffer->write(self::ESC.'m');
        }
        $this->lineFeed($lines);
    }

    /**
     * Implements barcodes 1D
     *
     * @param int    $type        Default CODE128
     * @param int    $height
     * @param int    $lineWidth
     * @param string $txtPosition
     * @param string $txtFont
     * @param string $data
     */
    public function barcode(
        $data = '123456',
        $type = 'CODE128',
        $height = 162,
        $lineWidth = 2,
        $txtPosition = 'none',
        $txtFont = ''
    ) {
        if ($this->printerMode == 'ESCPOS') {
            parent::barcode($data, $type, $height, $lineWidth, $txtPosition, $txtFont);
            return;
        }
        if (! $data = Barcodes\Barcode1DAnalysis::validate($data, $type)) {
            throw new \InvalidArgumentException('Data or barcode type is incorrect.');
        }
        if (! array_key_exists($type, $this->barcode1Dlist)) {
            throw new \InvalidArgumentException('This barcode type is not listed.');
        }
        $n = strlen($data);
        $id = $this->barcode1Dlist[$type];
        $height = self::validateInteger($height, 50, 200, 50);
        $lineWidth = self::validateInteger($lineWidth, 2, 5, 2);
        $n4 = 0;
        if ($txtPosition != 'none') {
            $n4 = 1;
        }
        switch ($type) {
            case 'UPC_A':
                $this->buffer->write(self::GS . 'kA' .self::VT . $data);
                break;
            case 'UPC_E':
                $this->buffer->write(self::GS . 'kB' .self::ACK . $data);
                break;
            case 'EAN13':
                $this->buffer->write(self::GS . 'kC' .self::FF . $data);
                break;
            case 'EAN8':
                $this->buffer->write(self::GS . 'kD' .self::BEL . $data);
                break;
            case 'CODE39':
                $this->buffer->write(self::GS . 'kE' . char($n) . $data);
                break;
            case 'I25':
                $this->buffer->write(self::GS . 'kF' . char($n) . $data);
                break;
            case 'CODABAR':
                $this->buffer->write(self::GS . 'kG' . char($n) . $data);
                break;
            case 'CODE93':
                $this->buffer->write(self::GS . 'kH' . char($n) . $data);
                break;
            case 'CODE128':
                $this->buffer->write(self::GS . 'kI' . char($n) . $data);
                break;
            case 'ISBN':
                $this->buffer->write(self::GS . 'k' . self::NAK . $data . self::NUL);
                break;
            case 'MSI':
                $this->buffer->write(self::GS . 'k' . self::SYN . $data . self::NUL);
                break;
        }
    }
    
    /**
     * Print PDF 417 2D barcode
     * @param string $data
     * @param integer $ecc
     * @param integer $pheight
     * @param integer $pwidth
     * @param integer $colunms
     * @return boolean
     */
    public function barcodePDF417($data = '', $ecc = 5, $pheight = 2, $pwidth = 2, $colunms = 3)
    {
        if (empty($data)) {
            return false;
        }
        if ($this->printerMode == 'ESCPOS') {
            parent::barcodePDF417($data, $ecc, $pheight, $pwidth, $colunms);
        }
        $ecc = self::validateInteger($ecc, 0, 8, 5);
        $pheight = self::validateInteger($pheight, 1, 8, 2);
        $pwidth = self::validateInteger($pwidth, 1, 4, 2);
        $length = strlen($data);
        $n6 = intval($length / 256);
        $n5 = ($length % 256);
        $this->buffer->write(
            self::GS
            . 'k'
            . chr(128)
            . chr($ecc)
            . chr($pheight)
            . chr($pwidth)
            . chr(0)
            . chr($n5)
            . chr($n6)
            . $data
        );
    }

    
    /**
     * Imprime o QR Code
     *
     * @param string $data   Dados a serem inseridos no QRCode
     * @param string $level  Nivel de correção L,M,Q ou H
     * @param int    $modelo modelo de QRCode 0 QRCode ou 1 microQR
     * @param int    $wmod   largura da barra 3 ~ 16
     */
    public function barcodeQRCode($data = '', $level = 'M', $modelo = 0, $wmod = 4)
    {
        $aModels = array();
        //essa matriz especifica o numero máximo de caracteres alfanumericos que o
        //modelo de QRCode suporta dependendo no nivel de correção.
        //Cada matriz representa um nivel de correção e cada uma das 40 posições nessas
        //matrizes indicam o numero do modelo do QRCode e o numero máximo de caracteres
        //alfamunéricos suportados
        //Quanto maior o nivel de correção menor é a quantidade de caracteres suportada
        $aModels[0]=[25,47,77,114,154,195,224,279,335,395,468,535,619,667,
            758,854,938,1046,1153,1249,1352,1460,1588,1704,1853,1990,2132,
            2223,2369,2520,2677,2840,3009,3183,3351,3537,3729,3927,4087,4296];
        $aModels[1]=[20,38,61,90,122,154,178,221,262,311,366,419,483,528,600,
            656,734,816,909,970,1035,1134,1248,1326,1451,1542,1637,1732,1839,
            1994,2113,2238,2369,2506,2632,2780,2894,3054,3220,3391];
        $aModels[2]=[16,29,47,67,87,108,125,157,189,221,259,296,352,376,426,
            470,531,574,644,702,742,823,890,963,1041,1094,1172,1263,1322,1429,
            1499,1618,1700,1787,1867,1966,2071,2181,2298,2420];
        $aModels[3]=[10,20,35,50,64,84,93,122,143,174,200,227,259,283,321,365,
            408,452,493,557,587,640,672,744,779,864,910,958,1016,1080,1150,1226,
            1307,1394,1431,1530,1591,1658,1774,1852];
        //n1 Error correction level (data restoration)
        switch ($level) {
            case 'L':
                $n1 = 0;
                break;
            case "M":
                $n1 = 1;
                break;
            case "Q":
                $n1 = 2;
                break;
            case "H":
                $n1 = 3;
                break;
            default:
                $n1 = 0;
        }
        if ($modelo != 0 && $modelo != 1) {
            $modelo = 0;
        }
        //se for mucroQR sua capacidade é bem reduzida
        if ($modelo == 1) {
            $aModels[0] = [6,14,21];
            $aModels[1] = [5,11,18];
            $aModels[2] = [0,0,13];
            $aModels[3] = [0,0,0];
        }
        //n2 Module/cell size in pixels MSB 1 ≤ module size ≤ 127 LSB 0 QR or 1 MicroQR
        $n2 = $wmod << 1;//shift 1 é o mesmo que multiplicar por 2
        $n2 += $modelo;//seleciona QRCode ou microQR
        //comprimento da mensagem
        $length = strlen($data);
        //seleciona matriz de modelos aplicavel pelo nivel de correção
        $am = $aModels[$n1];
        $i = 0;
        $flag = false;
        foreach ($am as $size) {
            //verifica se o tamanho maximo é maior ou igual ao comprimento da mensagem
            if ($size >= $length) {
                $flag = true;
                break;
            }
            $i++;
        }
        if (! $flag) {
            throw new InvalidArgumentException(
                'O numero de caracteres da mensagem é maior que a capacidade do QRCode'
            );
        }
        //n3 Version QRCode
        //depende do comprimento dos dados e do nivel de correção
        $n3 = ($i + 1);
        //n4 Encoding modes
        //0 – Numeric only              Max. 7,089 characters
        //1 – Alphanumeric              Max. 4,296 characters
        //2 – Binary (8 bits)           Max. 2,953 bytes
        //3 – Kanji, full-width Kana    Max. 1,817 characters
        $n4 = 1;//sempre será 1 apenas caracteres alfanumericos nesse caso
        //n5 e n6 Indicate the number of bytes that will be coded, where total = n5 + n6 x 256,
        //and total must be less than 7089.
        $n6 = intval($length / 256);
        $n5 = ($length % 256);
        $this->buffer->write(self::GS."kQ" . chr($n1) . chr($n2) . chr($n3) . chr($n4) . chr($n5) . chr($n6) . $data);
    }
}
