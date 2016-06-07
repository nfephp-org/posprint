<?php

namespace Posprint\Printers;

/**
 * Daruma class for POS printer
 * Model: DR700
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
use Posprint\Graphics\Graphics;

final class Daruma extends DefaultPrinter implements PrinterInterface
{
    /**
     * List all available code pages.
     *
     * @var array
     */
    protected $aCodePage = array(
        'ISO8859-1' => array('conv' => 'ISO8859-1', 'table' => '0', 'desc' => 'ISO8859-1: Latin1'),
        'CP437' => array('conv' => '437', 'table' => '3', 'desc' => 'PC437: USA, Standard Europe'),
        'CP850' => array('conv' => '850', 'table' => '1', 'desc' => 'PC850: Multilingual')
    );
    /**
     * List all available region pages.
     *
     * @var array
     */
    protected $aRegion = array(
        'LATIN',
    );
    /**
     * List all avaiable fonts
     *
     * @var array
     */
    protected $aFont = array(0 => 'normal', 1 => 'elite');
    /**
     * Selected internal font.
     *
     * @var string
     */
    protected $font = 'normal';
    /**
     * Seleted code page
     * Defined in printer class.
     *
     * @var string
     */
    protected $codepage = 'ISO8859-1';
    /**
     * Acceptable barcodes list
     * @var array
     */
    protected $barcode1Dlist = [
        'EAN13' => 1,
        'EAN8' => 2,
        'S25' => 3,
        'I25' => 4,
        'CODE128' => 5,
        'CODE39' => 6,
        'CODE93' => 7,
        'UPC_A' => 8,
        'CODABAR' => 9,
        'MSI' => 10,
        'CODE11' => 11
    ];
    /**
     * List of supported models
     * @var array
     */
    protected $modelList = [
        'DR600',
        'DR700'
    ];
    /**
     * Selected model
     * @var string
     */
    protected $printerModel = 'DR700';
    
    //public function __construct(); vide DefaultPrinter
    //public function defaultCodePage(); vide DefaultPrinter
    //public function defaultRegionPage(); vide DefaultPrinter
    //public function defaultFont(); vide DefaultPrinter
    //public function defaultModel(); vide DefaultPrinter
    //public function initialize(); vide DefaultPrinter
    //
    //          0000000000111111111122222222223333333333
    //          0123456789012345678901234567890123456789
    //[ESC] 228 0XXXX5678X0XXX45XXXXXXXXXXXXXXXXX3456XX9
    
    /**
     * Select printer mode
     *
     * @param string $mode
     */
    public function setPrintMode($mode = null)
    {
        //not used for this printer
    }
    
    //public function setCodePage(); vide DefaultPrinter
    
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
     *     ESC ! n
     *        n (BIT)           FUNÇÃO
     *            0 ..... 0      fonte normal
     *                    1      fonte elite
     *            3 ..... 0      desliga enfatizado
     *                    1      liga enfatizado
     *            4 ..... 0      desliga dupla altura
     *                    1      liga dupla altura
     *            5 ..... 0      desliga expandido
     *                    1      liga expandido
     *            7 ..... 0      desliga sublinhado
     *                    1      liga sublinhado
     *
     * @param string $font
     */
    public function setFont($font = null)
    {
        $font = $this->defaultFont($font);
        $fn = array_keys($this->aFont, $font, true);
        $mode = $fn[0];
        $mode += (8 * $this->boldMode);
        $mode += (16 * $this->doubleHeigth);
        $mode += (32 * $this->expandedMode);
        $mode += (128 * $this->underlineMode);
        $this->buffer->write(self::ESC.'!'.chr($mode));
    }

    /**
     * Set emphasys mode on or off.
     */
    public function setBold()
    {
        $this->boldMode = ! $this->boldMode;
        if ($this->boldMode) {
            $this->buffer->write(self::ESC . 'E');
        } else {
            $this->buffer->write(self::ESC . 'F');
        }
    }

    /**
     * Set Italic mode
     * Apenas para V.02.20.00 ou superior.
     */
    public function setItalic()
    {
        $n = 1;
        if ($this->italicMode) {
            $n = 0;
        }
        $this->italicMode = ! $this->italicMode;
        $this->buffer->write(self::ESC . '4' .chr($n));
    }

    /**
     * Set underline mode on or off.
     */
    public function setUnderlined()
    {
        $this->underlineMode = ! $this->underlineMode;
        $this->buffer->write(self::ESC . '-');
    }
    
    /**
     * Set or unset condensed mode.
     */
    public function setCondensed()
    {
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
        $this->buffer->write(self::ESC . 'W');
    }
    
    /**
     * Aligns all data in one line to the selected layout in standard mode.
     * L - left  C - center  R - rigth
     * OBS: O comando de justificação de texto desliga as configurações de margem.
     *      Apenas para V.02.20.00 ou superior.
     *
     * @param string $align
     */
    public function setAlign($align = null)
    {
        if (is_null($align)) {
            $align = 'L';
        }
        $value = strtoupper($align);
        switch ($value) {
            case 'C':
                $mode = 1;
                break;
            case 'R':
                $mode = 2;
                break;
            default:
                $mode = 0;
        }
        $this->buffer->write(self::ESC . 'j' . chr($mode));
    }
    
    /**
     * Turns white/black reverse print On or Off for characters.
     */
    public function setReverseColors()
    {
        //not used for this printer
    }

    /**
     * Set rotate 90 degrees.
     */
    public function setRotate90()
    {
        //not used for this printer
    }
    
    /**
     * Set horizontal and vertical motion units
     * $horizontal => character spacing 1/x"
     * $vertical => line spacing 1/y".
     * DLE A x y
     * Ajusta a unidade de movimento horizontal e vertical para aproximadamente
     *   25.4/x mm {1/x"} e 25.4/y mm {1/y"}. A unidade horizontal (x) não é utilizada
     * na impressora.
     *   Faixa: 0 ≤ x ≤ 255
     *          0 ≤ y ≤ 255
     * Padrão: x = 200 (sem uso na impressora)
     *         y = 400
     * Quando x e y são igual a zero, o valor padrão é carregado.
     *
     * @param int $horizontal
     * @param int $vertical
     */
    public function setSpacing($horizontal = 30, $vertical = 30)
    {
        $horizontal = self::validateInteger($horizontal, 0, 255, 30);
        $vertical = self::validateInteger($vertical, 0, 255, 30);
        $this->buffer->write(self::DLE.'A'.chr($horizontal).chr($vertical));
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
        $lines = self::validateInteger($lines, 0, 255, 1);
        for ($lin = 1; $lin <= $lines; $lin++) {
            $this->buffer->write(self::LF);
        }
    }

    //public function dotFeed(); vide default

    /**
     * Insert a image.
     * DLE X m xL xH yL yH d1....dk
     *  m   Mode    Vertical Dot Density    Horizontal Dot Density
     *  0 Normal         200 dpi                200 dpi
     *  1 Double-width   200 dpi                100 dpi
     *  2 Double-height  100 dpi                200 dpi
     *  3 Quadruple      100 dpi                100 dpi
     *
     * @param  string $filename Path to image file
     * @param  float  $width
     * @param  float  $height
     * @param  int    $size     0-normal 1-Double Width 2-Double Heigth 3-Quadruple
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
        $imgHeader = self::dataHeader(array($img->getWidth(), $img->getHeight()), true);
        $this->buffer->write(self::DLE . 'X' . chr($size) . $imgHeader . $img->getRasterImage());
    }
    
    /**
     * Generate a pulse, for opening a cash drawer if one is connected.
     *
     * @param int $pin    not for this printer
     * @param int $on_ms  not for this printer
     * @param int $off_ms not for this printer
     */
    public function pulse($pin = 0, $on_ms = 120, $off_ms = 240)
    {
        $this->buffer->write(self::ESC . 'p');
    }

    /**
     * Cut the paper.
     *
     * @param int $mode  FULL or PARTIAL. not for this printer.
     * @param int $lines Number of lines to feed after cut
     */
    public function cut($mode = 'PARTIAL', $lines = 3)
    {
        $this->buffer->write(self::ESC.'m');
        $this->lineFeed($lines);
    }
    
    /**
     * Implements barcodes 1D
     * ESC b n1 n2 n3 n4 s1...sn NULL
     *  n1 – tipo do código a ser impresso
     *      EAN13 1
     *      EAN8 2
     *      S2OF5 3
     *      I2OF5 4
     *      CODE128 5
     *      CODE39 6
     *      CODE93 7
     *      UPC_A 8
     *      CODABAR 9
     *      MSI 10
     *      CODE11 11
     *  n2 – largura da barra. De 2 a 5. Se 0, é usado 2.
     *  n3 – altura da barra. De 50 a 200. Se 0, é usado 50.
     *  n4 – se 1, imprime o código abaixo das barras
     *  s1...sn – string contendo o código.
     *      EAN-13: 12 dígitos de 0 a 9
     *      EAN–8: 7 dígitos de 0 a 9
     *      UPC–A: 11 dígitos de 0 a 9
     *      CODE 39 : Tamanho variável. 0-9, A-Z, '-', '.', '%', '/', '$', ' ', '+'
     *      O caracter '*' de start/stop é inserido automaticamente.
     *      Sem dígito de verificação MOD 43
     *      CODE 93: Tamanho variável. 0-9, A-Z, '-', '.', ' ', '$', '/', '+', '%'
     *      O caracter '*' de start/stop é inserido automaticamente.
     *      CODABAR: tamanho variável. 0 - 9, '$', '-', ':', '/', '.', '+'
     *      Existem 4 diferentes caracteres de start/stop: A, B, C, and D que são
     *      usados em pares e não podem aparecer em nenhum outro lugar do código.
     *      Sem dígito de verificação
     *      CODE 11: Tamanho variável. 0 a 9
     *      Checksum de dois caracteres.
     *      CODE 128: Tamanho variável. Todos os caracteres ASCII.
     *      Interleaved 2 of 5: tamanho sempre par. 0 a 9. Sem dígito de verificação
     *      Standard 2 of 5 (Industrial): 0 a 9. Sem dígito de verificação
     *      MSI/Plessey: tamanho variável. 0 - 9. 1 dígito de verificação
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
        if (! $data = Barcodes\Barcode1DAnalysis::validate($data, $type)) {
            throw new \InvalidArgumentException('Data or barcode type is incorrect.');
        }
        if (! array_key_exists($type, $this->barcode1Dlist)) {
            throw new \InvalidArgumentException('This barcode type is not listed.');
        }
        $id = $this->barcode1Dlist[$type];
        $height = self::validateInteger($height, 50, 200, 50);
        $lineWidth = self::validateInteger($lineWidth, 2, 5, 2);
        $n4 = 0;
        if ($txtPosition != 'none') {
            $n4 = 1;
        }
        $this->buffer->write(self::ESC . 'b' . chr($id) . chr($lineWidth) . chr($height) . chr($n4) . $data);
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
        $length = strlen($data)+6;
        if ($length > 906) {
            return false;
        }
        $pheight = self::validateInteger($pheight, 1, 8, 2);
        $pwidth = self::validateInteger($pwidth, 1, 8, 2);
        $pH = intval($length / 256);
        $pL = ($length % 256);
        //[ESC] <128> <–Size><+Size> <–Columns><+Columns> <–Height><+Height> <–Width><+Width>
        //<D001> <D002> . . . <Dnnn>
        //Size inclui os demais 6 bytes de controle
        //Size ≤ 906
        //nnn = Size – 6
        $cH = intval($colunms / 256);
        $cL = ($colunms % 256);
        $hH = intval($pheight / 256);
        $hL = ($pheight % 256);
        $wH = intval($pwidth / 256);
        $wL = ($pwidth % 256);
        
        $this->buffer->write(self::ESC
            . chr(128)
            . chr($pL)
            . chr($pH)
            . chr($cL)
            . chr($cH)
            . chr($hL)
            . chr($hH)
            . chr($wL)
            . chr($wH)
            . $data);
    }
    
    /**
     * Print QR Code
     * [ESC] <129> <–Size><+Size> <Width> <Ecc> <D001> <D002> . . . <Dnnn>
     * Size inclui os 2 bytes de controle
     *    Size ≤ 402
     *    nnn = Size – 2
     *   Largura do módulo (Width): 0, 4 ≤ Width ≤ 7 ( =0 para default = 5)
     *   Redundância (ECC): 0, M, Q, H ( =0 para cálculo automático)
     * Apenas para V.02.50.00 ou superior.
     *
     * @param string $data   Dados a serem inseridos no QRCode
     * @param string $level  Nivel de correção L,M,Q ou H
     * @param int    $modelo modelo de QRCode none
     * @param int    $wmod   largura da barra 4 ~ 7
     */
    public function barcodeQRCode($data = '', $level = 'L', $modelo = 2, $wmod = 4)
    {
        $len = strlen($data);
        $size = $len + 2;
        if ($size > 402) {
            return false;
        }
        $nH = round($size/256, 0);
        $nL = $size%256;
        if ($wmod > 7 || $wmod < 4) {
            $wmod = 5;
        }
        //set error correction level
        $level = strtoupper($level);
        switch ($level) {
            case 'M':
            case 'Q':
            case 'H':
                $ecc = $level;
                break;
            default:
                $ecc = 0;
        }
        $this->buffer->write(self::ESC . chr(129) . chr($nL) . chr($nH) . chr($wmod) . $ecc . $data);
    }
    //public function send(); vide DefultPrinter
    //public function close(); vide DefaultPrinter
}
