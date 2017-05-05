<?php

namespace Posprint\Printers;

/*
 * Default class for POS thermal printers.
 * 
 * From this class all other are extended.
 * In the child classes should be included all the commands that are different
 * from those in this class, especially those specific to particular brand and 
 * model of printer
 * 
 * NOTE: It was built around the commands of the Epson TM-T20,
 * so in theory the Epson class will be almost empty just extending this class.
 * 
 * CodePage default CP437
 * CountyPage default LATIN
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Printers\PrinterInterface;
use Posprint\Connectors\ConnectorInterface;
use Posprint\Connectors\Buffer;
use Posprint\Graphics\Graphics;
use Posprint\Printers\Barcodes\Barcode1DAnalysis;
use RuntimeException;
use InvalidArgumentException;

abstract class DefaultPrinter implements PrinterInterface
{
    //set standards
    const NUL = "\x0"; //Nulo
    const EOT = "\x4"; //EOT fim da transmissão
    const ENQ = "\x5"; //ENQ colocar na fila Pedido de status 1
    const HT = "\x9"; //tabulação horizontal
    const VT = "\xb"; //tabulação vertical
    const LF = "\x0a"; //Inicia a impressão e avança uma linha
    const FF = "\x0c"; //avança pagina
    const CR = "\x0d"; //retorno de carro
    const DLE = "\x10"; //Data Link Escape
    const CAN = "\x18"; //CAN Cancela linha enviada
    const BEL = "\x07"; //BEL sinal sonoro
    const ESC = "\x1b"; //escape
    const FS = "\x1c"; //FS
    const GS = "\x1d"; //GS
    const SO = "\x0e"; //SO Inicia modo expandido
    const DC1 = "\x11"; //DC1 Inicia modo enfatizado
    const DC2 = "\x12"; //DC2 Cancela modo condensado
    const DC3 = "\x13"; //DC3 Cancela modo enfatizado
    const DC4 = "\x14"; //DC4 Controle de dispositivo 4 Inicia modo normal
    const SI = "\x0f"; //Seleciona modo condensado
    const EM = "\x19"; //Avança 4 linhas
    const DEL = "\x7f"; //Cancela último caracter
    const NAK = "\x15"; //
    const SYN = "\x16"; //Sincronismo
    const NOTRANS = false; //not translate characters codepage
    const TRANS = true; //perform a character convertion to codepage

    //Cut types
    const CUT_FULL = 65;
    const CUT_PARTIAL = 66;
    
    //Image sizing options
    const IMG_DEFAULT = 0;
    const IMG_DOUBLE_WIDTH = 1;
    const IMG_DOUBLE_HEIGHT = 2;

    /**
     * List all available region pages.
     *
     * @var array
     */
    protected $aRegion = array(
        'USA',
        'FRANCE',
        'GERMANY',
        'UK',
        'DENMARK',
        'SWEDEN',
        'ITALY',
        'SPAIN',
        'JAPAN',
        'NORWAY',
        'DENMARK2',
        'SPAIN2',
        'LATIN',
        'KOREA',
        'SLOVENIA',
        'CHINA',
        'VIETNAM',
        'ARABIA'
    );

    /**
     * List all available code pages.
     *
     * @var array
     */
    protected $aCodePage = array(
        'CP437' => array('conv' => '437', 'table' => '0', 'desc' => 'PC437: USA, Standard Europe'),
        'CP850' => array('conv' => '850', 'table' => '2', 'desc' => 'PC850: Multilingual'),
        'CP860' => array('conv' => '860', 'table' => '3', 'desc' => 'PC860: Portuguese'),
        'CP863' => array('conv' => '863', 'table' => '4', 'desc' => 'PC863: Canadian-French'),
        'CP865' => array('conv' => '865', 'table' => '5', 'desc' => 'PC865: Nordic'),
        'CP851' => array('conv' => '851', 'table' => '11', 'desc' => 'PC851: Greek'),
        'CP853' => array('conv' => '853', 'table' => '12', 'desc' => 'PC853: Turkish'),
        'CP857' => array('conv' => '857', 'table' => '13', 'desc' => 'PC857: Turkish'),
        'CP737' => array('conv' => '737', 'table' => '14', 'desc' => 'PC737: Greek'),
        'ISO8859-7' => array('conv' => 'ISO8859-7', 'table' => '15', 'desc' => 'ISO8859-7: Greek'),
        'CP866' => array('conv' => '866', 'table' => '17', 'desc' => 'PC866: Cyrillic #2'),
        'CP852' => array('conv' => '852', 'table' => '18', 'desc' => 'PC852: Latin2'),
        'CP858' => array('conv' => '858', 'table' => '19', 'desc' => 'PC858: Euro'),
        'CP720' => array('conv' => '720', 'table' => '32', 'desc' => 'PC720: Arabic'),
        'CP855' => array('conv' => '855', 'table' => '34', 'desc' => 'PC855: Cyrillic'),
        'CP861' => array('conv' => '861', 'table' => '35', 'desc' => 'PC861: Icelandic'),
        'CP862' => array('conv' => '862', 'table' => '36', 'desc' => 'PC862: Hebrew'),
        'CP864' => array('conv' => '864', 'table' => '37', 'desc' => 'PC864: Arabic'),
        'CP869' => array('conv' => '869', 'table' => '38', 'desc' => 'PC869: Greek'),
        'ISO8859-2' => array('conv' => 'ISO8859-2', 'table' => '39', 'desc' => 'ISO8859-2: Latin2'),
        'ISO8859-15' => array('conv' => 'ISO8859-15', 'table' => '40', 'desc' => 'ISO8859-15: Latin9'),
        'WINDOWS-1250' => array('conv' => 'WINDOWS-1250', 'table' => '45', 'desc' => 'WPC1250: Latin2'),
        'WINDOWS-1251' => array('conv' => 'WINDOWS-1251', 'table' => '46', 'desc' => 'WPC1251: Cyrillic'),
        'WINDOWS-1252' => array('conv' => 'WINDOWS-1252', 'table' => '47', 'desc' => 'WPC1253: Greek'),
        'WINDOWS-1254' => array('conv' => 'WINDOWS-1254', 'table' => '48', 'desc' => 'WPC1254: Turkish'),
        'WINDOWS-1255' => array('conv' => 'WINDOWS-1255', 'table' => '49', 'desc' => 'WPC1255: Hebrew'),
        'WINDOWS-1256' => array('conv' => 'WINDOWS-1256', 'table' => '50', 'desc' => 'WPC1256: Arabic'),
        'WINDOWS-1257' => array('conv' => 'WINDOWS-1257', 'table' => '51', 'desc' => 'WPC1257: Baltic Rim'),
        'WINDOWS-1258' => array('conv' => 'WINDOWS-1258', 'table' => '52', 'desc' => 'WPC1258: Vietnamese'),
    );
    
    /**
     * Seleted code page
     * Defined in printer class.
     *
     * @var string
     */
    protected $codepage = 'CP437';
    /**
     * Number of codpage in printer memory.
     *
     * @var int
     */
    protected $charsetTableNum = 0;
    /**
     * Selected Region character page
     * Defined in printer class.
     *
     * @var string
     */
    protected $region = 'LATIN';
    /**
     * List all avaiable fonts
     *
     * @var array
     */
    protected $aFont = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 97 => 'SA', 98 => 'SB');
    /**
     * Selected internal font.
     *
     * @var string
     */
    protected $font = 'A';
    /**
     * Resolution in dpi.
     *
     * @var int
     */
    public $dpi = 203; //dots per inch
    /**
     * Resolution in dpmm.
     *
     * @var int
     */
    public $dpmm = 8; //dots per mm
    /**
     * Maximum width paper.
     *
     * @var int
     */
    public $widthMaxmm = 80;//mm
    /**
     * Selected Width paper.
     *
     * @var int
     */
    public $widthPaper = 80;//mm
    /**
     * Maximum width for printed area.
     *
     * @var int
     */
    public $widthPrint = 72;//mm
    /**
     * Maximum width for printed area in dots.
     *
     * @var int
     */
    public $widthMaxdots = 576;//dots
    /**
     * Maximum number of characters per line.
     *
     * @var int
     */
    public $maxchars = 48;//max characters per line

    //protected property standards
    /**
     * Connector to printer.
     *
     * @var ConnectosInterface
     */
    protected $connector = null;
    /**
     * Seleted printer mode.
     *
     * @var string
     */
    protected $printerMode = 'normal';
    /**
     * Selected bold mode.
     *
     * @var bool
     */
    protected $boldMode = false;
    /**
     * Selected italic mode.
     *
     * @var bool
     */
    protected $italicMode = false;
    /**
     * Selected condenced mode.
     *
     * @var bool
     */
    protected $condensedMode = false;
    /**
     * Selected expanded mode.
     * @var bool
     */
    protected $expandedMode = false;
    /**
     * Seleted double higth mode.
     * @var bool
     */
    protected $doubleHeigth = false;
    /**
     * Selected reverse colors mode.
     *
     * @var bool
     */
    protected $reverseColors = false;
    /**
     * Selected under lined mode.
     *
     * @var bool
     */
    protected $underlineMode = false;
    /**
     * Selected rotate 90 degrees mode
     *
     * @var bool
     */
    protected $rotateMode = false;
    /**
     * Buffer class.
     *
     * @var Connectors\Buffer
     */
    protected $buffer = null;
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
        'GS1128' => 74,
        'GS1DATABAROMINI' => 75,
        'GS1DATABARTRUNC' => 76,
        'GS1DATABARLIMIT' => 77,
        'GS1DATABAREXPAN' => 78
    ];
    /**
     * List of supported models
     * @var array
     */
    protected $modelList = [
        'T20'
    ];
    /**
     * Selected model
     * @var string
     */
    protected $printerModel = 'T20';

    /**
     * Class constructor
     * Instantiates the data buffer.
     *
     * @param ConnectorInterface $conn
     */
    public function __construct(ConnectorInterface $conn = null)
    {
        if (!is_null($conn)) {
            $this->connector = $conn;
        }
        $this->buffer = new Buffer();
    }
    
    /**
     * Return default printer model
     * @param string $model
     * @return string|array
     */
    public function defaultModel($model = 'T20')
    {
        if (!is_null($model)) {
            $model = strtoupper(trim($model));
            if ($model == 'ALL') {
                return $this->modelList;
            }
        }
        if (array_key_exists($model, $this->modelList)) {
            $this->printerModel = $model;
        }
        return $model;
    }
    
    /**
     * Returns a default region for codepage
     * if param $region is null will return actual default region from class
     * if param $region is 'all' will return a array with all avaiable regions
     * if param $region is a string will set the region parameter of class and returns it.
     * NOTE: This command do not set the printer, only class parameters
     *
     * @param  string $region
     * @return string|array
     */
    public function defaultRegionPage($region = null)
    {
        if (!is_null($region)) {
            $region = strtoupper(trim($region));
            if ($region == 'ALL') {
                return $this->aRegion;
            }
            $reg = array_search($region, $this->aRegion, true);
            if ($reg !== false) {
                $this->region = $region;
            }
        }
        return $this->region;
    }

    /**
     * Returns a default codepage
     * if param $codepage is null will return actual default codepage from class
     * if param $codepage is 'all' will return a array with all avaiable codepages
     * if param $codepage is a string will set the codepage parameter of class and returns it.
     * NOTE: This command do not set the printer, only class parameters
     *
     * @param  string $codepage
     * @return string|array
     */
    public function defaultCodePage($codepage = null)
    {
        if (!is_null($codepage)) {
            $codepage = strtoupper(trim($codepage));
            if ($codepage == 'ALL') {
                return array_keys($this->aCodePage);
            }
            if (array_key_exists($codepage, $this->aCodePage)) {
                $this->codepage = $codepage;
                $table = $this->aCodePage[$codepage];
                $this->charsetTableNum = $table['table'];
            }
        }
        return $this->codepage;
    }

    /**
     * Returns the default printer font
     * A - Font A (12 x 24)
     * B - Font B (9 x 17)
     * C - Font C
     * D - Font D
     * E - Font E
     * Special A
     * Special B
     * Default Font A.
     * if param $font is null will return actual default font from class
     * if param $font is 'all' will return a array with all avaiable printer fonts
     * if param $font is a string will set the font parameter of class and returns it.
     * NOTE: This command do not set the printer, only class parameters
     *
     * @param  string $font
     * @return array|string
     */
    public function defaultFont($font = null)
    {
        if (!is_null($font)) {
            $font = strtoupper(trim($font));
            if ($font == 'ALL') {
                //return array
                return $this->aFont;
            }
            //set $this->font
            $fonts = array_flip($this->aFont);
            $keys = array_keys($fonts);
            $reg = array_search($font, $keys, true);
            if ($reg !== false) {
                $this->font = $font;
            }
        }
        return $this->font;
    }

    /**
     * initialize printer
     * Clears the data in the print buffer and resets the printer modes to
     * the modes that were in effect when the power was turned on.
     */
    public function initialize()
    {
        $this->rotateMode = false;
        $this->boldMode = false;
        $this->italicMode = false;
        $this->underlineMode = false;
        $this->printerMode = 'normal';
        $this->defaultModel();
        $this->defaultCodePage();
        $this->defaultRegionPage();
        $this->defaultFont();
        $this->buffer->write(self::ESC.'@');
        $this->setPrintMode();
        $this->setFont();
        $this->setCodePage();
        $this->setRegionPage();
    }

    /**
     * Set the printer mode.
     */
    abstract public function setPrintMode($mode = null);
    
    /**
     * Set a codepage table in printer.
     *
     * @param string $codepage
     */
    public function setCodePage($codepage = null)
    {
        $codepage = $this->defaultCodePage($codepage);
        $this->buffer->write(self::ESC.'t'.chr($this->charsetTableNum));
    }

    /**
     * Set a region page.
     * The numeric key of array $this->aRegion is the command parameter.
     *
     * @param string $region
     */
    public function setRegionPage($region = null)
    {
        $region = $this->defaultRegionPage($region);
        $mode = array_keys($this->aRegion, $region, true);
        $this->buffer->write(self::ESC.'R'.chr($mode[0]));
    }
    
    /**
     * Set a printer font
     * If send a valid font name will set the printer otherelse a default font is selected
     *
     * @param string $font
     */
    public function setFont($font = null)
    {
        $font = $this->defaultFont($font);
        $mode = array_keys($this->aFont, $font, true);
        $this->buffer->write(self::ESC.'M'.chr($mode[0]));
    }

    /**
     * Set emphasys mode on or off.
     */
    public function setBold()
    {
        $mode = 1;
        if ($this->boldMode) {
            $mode = 0;
        }
        $this->boldMode = ! $this->boldMode;
        $this->buffer->write(self::ESC . 'E' . chr($mode));
    }

    /**
     * Set underline mode on or off.
     */
    public function setUnderlined()
    {
        $mode = 1;
        if ($this->underlineMode) {
            $mode = 0;
        }
        $this->underlineMode = ! $this->underlineMode;
        $this->buffer->write(self::ESC . '-' . chr($mode));
    }
    
    /**
     * Set italic mode on or off
     *
     * @return bool
     */
    public function setItalic()
    {
        //dont exists in this printer
    }
    
    /**
     * Aligns all data in one line to the selected layout in standard mode.
     * L - left  C - center  R - rigth
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
        $this->buffer->write(self::ESC . 'a' . chr($mode));
    }
    
    /**
     * Turns white/black reverse print On or Off for characters.
     * n = odd: On, n = even: Off.
     */
    public function setReverseColors()
    {
        $mode = 0;
        $this->reverseColors = ! $this->reverseColors;
        if ($this->reverseColors) {
            $mode = 1;
        }
        $this->buffer->write(self::GS.'B'.chr($mode));
    }
    
    /**
     * Set expanded mode.
     *
     * @param int $size multiplies normal size 1 - 8
     */
    public function setExpanded($size = null)
    {
        $size = self::validateInteger($size, 1, 8, 1);
        $aSize = [
            [0, 0],
            [16, 1],
            [32, 2],
            [48, 3],
            [64, 4],
            [80, 5],
            [96, 6],
            [112, 7]
        ];
        $mode = $aSize[$size-1][0] + $aSize[$size-1][1];
        $this->buffer->write(self::ESC.'!'.chr($mode));
    }

    /**
     * Set condensed mode.
     */
    public function setCondensed()
    {
        $this->setExpanded(1);
        $this->setFont('B');
    }
    
    /**
     * Set rotate 90 degrees.
     */
    public function setRotate90()
    {
        $this->rotateMode = !$this->rotateMode;
        $mode = 0;
        if ($this->rotateMode) {
            $mode = 1;
        }
        $this->buffer->write(self::ESC.'V'.chr($mode));
    }
    
    /**
     * Send message or command to buffer
     * when sending commands is not required to convert characters,
     * so the variable may translate by false.
     *
     * @param string $text
     */
    public function text($text = '')
    {
        $text = $this->translate($text);
        $this->buffer->write($text);
    }

    /**
     * Set horizontal and vertical motion units
     * $horizontal => character spacing 1/x"
     * $vertical => line spacing 1/y".
     *
     * @param int $horizontal
     * @param int $vertical
     */
    public function setSpacing($horizontal = 30, $vertical = 30)
    {
        $horizontal = self::validateInteger($horizontal, 0, 255, 30);
        $vertical = self::validateInteger($vertical, 0, 255, 30);
        $this->buffer->write(self::GS.'P'.chr($horizontal).chr($vertical));
    }

    /**
     * Set right-side character spacing
     * 0 ≤ n ≤ 255 => 1/x".
     *
     * @param int $value
     */
    public function setCharSpacing($value = 3)
    {
        $value = self::validateInteger($value, 0, 255, 0);
        $this->buffer->write(self::ESC.' '.chr($value));
    }

    /**
     * Line spacing
     * The default is set to zero and 30/180 "
     * any different number of zero will generate multiples of.
     * n  1/180-inch vertical motion
     * normal paragraph 30/180" => 4.23 mm
     *
     * @param int $paragraph
     */
    public function setParagraph($value = 0)
    {
        $value = self::validateInteger($value, 0, 255, 0);
        $paragraph = ceil($value);
        if ($paragraph == 0) {
            $this->buffer->write(self::ESC.'2');
            return;
        }
        if ($paragraph < 25) {
            $paragraph = 25;
        } elseif ($paragraph > 255) {
            $paragraph = 255;
        }
        $this->buffer->write(self::ESC.'3'.chr($paragraph));
    }

    /**
     * Prints data and feeds paper n lines
     * ESC d n Prints data and feeds paper n lines.
     *
     * @param integer $lines
     */
    public function lineFeed($lines = 1)
    {
        $lines = self::validateInteger($lines, 0, 255, 1);
        if ($lines == 1) {
            $this->buffer->write(self::LF);
            return;
        }
        $this->buffer->write(self::ESC.'d'.chr($lines));
    }

    /**
     * Prints data and feeds paper n dots
     * ESC J n Prints data and feeds paper n dots.
     *
     * @param int $dots
     */
    public function dotFeed($dots = 1)
    {
        $dots = self::validateInteger($dots, 0, 80, 0);
        $this->buffer->write(self::ESC.'J'.chr($dots));
    }

    /**
     * Generate a pulse, for opening a cash drawer if one is connected.
     * The default settings should open an Epson drawer.
     *
     * @param int $pin    0 or 1, for pin 2 or pin 5 kick-out connector respectively.
     * @param int $on_ms  pulse ON time, in milliseconds.
     * @param int $off_ms pulse OFF time, in milliseconds.
     */
    public function pulse($pin = 0, $on_ms = 120, $off_ms = 240)
    {
        $pin = self::validateInteger($pin, 0, 1, 0);
        $on_ms = self::validateInteger($on_ms, 1, 511, 120);
        $off_ms = self::validateInteger($off_ms, 1, 511, 240);
        $this->buffer->write(self::ESC.'p'.chr($pin + 48).chr($on_ms / 2).chr($off_ms / 2));
    }

    /**
     * Cut the paper.
     *
     * @param int $mode  FULL or PARTIAL. If not specified, FULL will be used.
     * @param int $lines Number of lines to feed after cut
     */
    public function cut($mode = 'PARTIAL', $lines = 3)
    {
        $lines = self::validateInteger($lines, 1, 10, 3);
        if ($mode == 'FULL') {
            $mode = self::CUT_FULL;
        } else {
            $mode = self::CUT_PARTIAL;
        }
        $this->buffer->write(self::GS.'V'.chr($mode).chr($lines));
    }

    /**
     * Implements barcodes 1D
     * GS k m n d1...dn
     * Prints bar code. n specifies the data length.
     *   m    bar code system             number of d (=k)
     *  "A"     UPC-A                       11 or 12
     *  "B"     UPC-E                       6, 7, 8, 11 or 12
     *  "C"     JAN13 / EAN13               12 or 13
     *  "D"     JAN8 / EAN8                 7 or 8
     *  "E"     CODE39                      1 or more
     *  "F"     ITF                         even
     *  "G"     CODABAR (NW-7)              2 or more
     *  "H"     CODE93                      1–255
     *  "I"     CODE128                     2–255
     *  "J"     GS1-128                     2–255
     *  "K"     GS1 DataBar Omnidirectional 13
     *  "L"     GS1 DataBar Truncated       13
     *  "M"     GS1 DataBar Limited         13
     *  "N"     GS1 DataBar Expanded        2–255.
     *
     *  GS h n Sets bar code height to n dots.
     *  GS w n Sets bar width of bar code. n = 2–6 (thin–thick)
     *  GS H n Selects print position of HRI characters.
     *           n = 0, "0": Not printed
     *           n = 1, "1": Above the bar code
     *           n = 2, "2": Below the bar code
     *           n = 3, "3": Both above and below the bar code
     *  GS f n Selects font for the HRI characters.
     *           n = 0, "0": Font A,
     *           n = 1, "1": Font B
     *
     * @param string $data
     * @param int    $type        Default CODE128
     * @param int    $height
     * @param int    $lineWidth
     * @param string $txtPosition
     * @param string $txtFont
     */
    public function barcode(
        $data = '123456',
        $type = 'CODE128',
        $height = 162,
        $lineWidth = 2,
        $txtPosition = 'none',
        $txtFont = ''
    ) {
        switch ($txtPosition) {
            case 'Above':
                $tPos = 1;
                break;
            case 'Below':
                $tPos = 2;
                break;
            case 'Both':
                $tPos = 3;
                break;
            default:
                //none
                $tPos = 0;
        }
        $font = 0;
        if ($txtFont === 'B') {
            $font = 1;
        }
        if (! $data = Barcodes\Barcode1DAnalysis::validate($data, $type)) {
            throw new \InvalidArgumentException('Data or barcode type is incorrect.');
        }
        if (! array_key_exists($type, $this->barcode1Dlist)) {
            throw new \InvalidArgumentException('This barcode type is not listed.');
        }
        $id = $this->barcode1Dlist[$type];
        if (is_null($id)) {
            return;
        }
        $height = self::validateInteger($height, 1, 255, 4);
        $lineWidth = self::validateInteger($lineWidth, 1, 6, 2);
        $nlen = strlen($data);
        //set barcode height
        $this->buffer->write(self::GS.'h'.chr($height));
        //set barcode bar width
        $this->buffer->write(self::GS.'w'.chr($lineWidth));
        //Selects print position of HRI characters.
        $this->buffer->write(self::GS.'H'.chr($tPos));
        //Selects font for the HRI characters.
        $this->buffer->write(self::GS.'f'.chr($font));
        //Print barcode
        $this->buffer->write(self::GS.'k'.chr($id).chr($nlen).$data);
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
        $ecc = self::validateInteger($ecc, 0, 8, 5);
        $pheight = self::validateInteger($pheight, 1, 8, 2);
        $n = $ecc + 48;
        $length = strlen($data);
        $pH = intval($length / 256);
        $pL = ($length % 256);
        //Set the number of columns in the data region
        //GS (   k  pL pH cn fn n
        //29 40 107 3   0 48 65 n
        $this->buffer->write(self::GS."(k".chr(3).chr(0).chr(48).chr(65).chr(0));
        //Set the number of rows
        //GS  (      k   pL  pH  cn fn n
        //29  40    107  3    0  48 66 n
        $this->buffer->write(self::GS."(k".chr(3).chr(0).chr(48).chr(66).chr(0));
        //Set the width of the module
        //GS  (   k   pL   pH  cn  fn n
        //29  40 107  3    0   48  67 n
        $this->buffer->write(self::GS."(k".chr(3).chr(0).chr(48).chr(67).chr(0));
        //Set the row height
        //GS  (    k   pL  pH  cn  fn n
        //29  40  107  3   0   48  68 n
        //pheight 3 or 5 time pwidth
        $this->buffer->write(self::GS."(k".chr(3).chr(0).chr(48).chr(68).chr($pheight));
        //Set the error correction level
        //GS  (    k    pL  pH     cn fn m n
        //29  40  107    4   0     48 69 m n n = 48 - 56
        $this->buffer->write(self::GS."(k".chr(4).chr(0).chr(48).chr(69).chr(58).chr($n));
        //Select the options
        //GS  (    k    pL   pH   cn   fn   n
        //29  40  107   3     0   48   70   n
        $this->buffer->write(self::GS."(k".chr(3).chr(0).chr(48).chr(70).chr(0));
        //Store the data in the symbol storage area
        //GS  (   k   pL  pH   cn  fn  m   d1...dk
        //29  40 107  pL  pH   48  80  48  d1...dk
        $this->buffer->write(self::GS."(k".chr($pL).chr($pH).chr(48).chr(80).chr(48).$data);
        //Print the symbol data in the symbol storage area
        //GS  (   k   pL  pH  cn  fn m
        //29  40 107  3    0   48  81 m
        $this->buffer->write(self::GS."(k".chr(3).chr(0).chr(48).chr(81).chr(0));
    }
  
    /**
     * Prints QRCode
     *
     * @param string $data   barcode data
     * @param string $level  correction level L,M,Q ou H
     * @param int    $modelo QRCode model 1, 2 ou 0 Micro
     * @param int    $wmod   width bar 3 ~ 16
     */
    public function barcodeQRCode($data = '', $level = 'L', $modelo = 2, $wmod = 4)
    {
        //set model of QRCode
        $n1 = 50;
        if ($modelo == 1) {
            $n1 = 49;
        }
        //select QR model
        $this->buffer->write(self::GS."(k".chr(4).chr(0).chr(49).chr(65).chr($n1).chr(0));
        //set module bar width
        $this->buffer->write(self::GS."(k".chr(3).chr(0).chr(49).chr(67).chr($wmod));
        //set error correction level
        $level = strtoupper($level);
        switch ($level) {
            case 'L':
                $n = 48;
                break;
            case 'M':
                $n = 49;
                break;
            case 'Q':
                $n = 50;
                break;
            case 'H':
                $n = 51;
                break;
            default:
                $n = 49;
        }
        $this->buffer->write(self::GS."(k".chr(3).chr(0).chr(49).chr(69).chr($n));
        //set data for QR Code assuming print only alphanumeric data
        $len = strlen($data) + 3;
        $pH = ($len / 256);
        $pL = $len % 256;
        $this->buffer->write(self::GS."(k".chr($pL).chr($pH).chr(49).chr(80).chr(48).$data);
        //Print QR Code
        $this->buffer->write(self::GS."(k".chr(3).chr(0).chr(49).chr(81).chr(48));
    }

    /**
     * Return all data buffer.
     *
     * @param string $type specifies the return format
     */
    public function getBuffer($type = '')
    {
        switch ($type) {
            case 'binA':
                //returns a binary array of buffer
                $resp = $this->buffer->getDataBinary(true);
                break;
            case 'binS':
                //returns a binary string of buffer
                $resp = $this->buffer->getDataBinary(false);
                break;
            case 'b64A':
                //returns a base64 encoded array of buffer
                $resp = $this->buffer->getDataBase64(true);
                break;
            case 'b64S':
                //returns a base64 encoded string of buffer
                $resp = $this->buffer->getDataBase64(false);
                break;
            case 'json':
                //returns a json encoded of array buffer
                $resp = $this->buffer->getDataJson();
                break;
            case 'readA':
                //returns a human readable format of array buffer
                //only for debug reasons
                $resp = $this->buffer->getDataReadable(true);
                break;
            default:
                //returns a human readable format of string buffer
                //only for debug reasons
                $resp = $this->buffer->getDataReadable(false);
        }
        return $resp;
    }

    /**
     * Send commands from buffer to connector printer.
     */
    public function send(ConnectorInterface $conn = null)
    {
        if (!is_null($conn)) {
            $this->connector = $conn;
        }
        if (is_null($this->connector)) {
            return $this->getBuffer();
        }
        $aCmds = $this->getBuffer('binA');
        foreach ($aCmds as $cmd) {
            $this->connector->write($cmd);
        }
    }

    /**
     * Insert a image.
     *
     * @param  string $filename Path to image file
     * @param  float  $width
     * @param  float  $height
     * @param  int    $size     0-normal 1-Double Width 2-Double Heigth
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
        $tone = '0';
        $colors = '1';
        $xm = (($size & self::IMG_DOUBLE_WIDTH) == self::IMG_DOUBLE_WIDTH) ? chr(2) : chr(1);
        $ym = (($size & self::IMG_DOUBLE_HEIGHT) == self::IMG_DOUBLE_HEIGHT) ? chr(2) : chr(1);
        $header = $tone.$xm.$ym.$colors.$imgHeader;
        $this->sendGraphicsData('0', 'p', $header.$img->getRasterImage());
        $this->sendGraphicsData('0', '2');
    }

    /**
     * Close and clean buffer
     * All data will be lost.
     */
    public function close()
    {
        $this->buffer->close();
    }
    
    /**
     * Wrapper for GS ( L, to calculate and send correct data length.
     *
     * @param string $m    Modifier/variant for function. Usually '0'.
     * @param string $fn   Function number to use, as character.
     * @param string $data Data to send.
     */
    protected function sendGraphicsData($m, $fn, $data = '')
    {
        $header = $this->intLowHigh(strlen($data) + 2, 2);
        $this->buffer->write(self::GS.'(L'.$header.$m.$fn.$data);
    }

    /**
     * Generate two characters for a number:
     * In lower and higher parts, or more parts as needed.
     *
     * @param int $int    Input number
     * @param int $length The number of bytes to output (1 - 4).
     */
    protected static function intLowHigh($input, $length)
    {
        $maxInput = (256 << ($length * 8) - 1);
        $outp = '';
        for ($i = 0; $i < $length; ++$i) {
            $outp .= chr($input % 256);
            $input = (int) ($input / 256);
        }
        return $outp;
    }

    /**
     * Convert widths and heights to characters.
     * Used before sending graphics to set the size.
     *
     * @param  array $inputs
     * @param  bool  $long   True to use 4 bytes, false to use 2
     * @return string
     */
    protected static function dataHeader(array $inputs, $long = true)
    {
        $outp = array();
        foreach ($inputs as $input) {
            if ($long) {
                $outp[] = self::intLowHigh($input, 2);
            } else {
                $input = self::validateInteger($input, 0, 255, 0);
                $outp[] = chr($input);
            }
        }
        return implode('', $outp);
    }

    /**
     * Verify if the argument given is not a boolean.
     *
     * @param  bool $test    the input to test
     * @param  bool $default the default value
     * @return bool
     */
    protected static function validateBoolean($test, $default)
    {
        if (!($test === true || $test === false)) {
            return $default;
        }
        return $test;
    }

    /**
     * Verify if the argument given is not an integer within the specified range.
     * will return default instead
     *
     * @param  int $test    the input to test
     * @param  int $min     the minimum allowable value (inclusive)
     * @param  int $max     the maximum allowable value (inclusive)
     * @param  int $default the default value
     * @return int
     */
    protected static function validateInteger($test, $min, $max, $default)
    {
        if (!is_integer($test) || $test < $min || $test > $max) {
            return $default;
        }
        return $test;
    }

    /**
     * Verify if the argument given can't be cast to a string.
     *
     * @param  string $test    the input to test
     * @param  string $default the default value
     * @return string
     */
    protected static function validateString($test, $default)
    {
        if (is_object($test) && !method_exists($test, '__toString')) {
            return $default;
        }
        return $test;
    }
    
    /**
     * Translate the text from UTF-8 for the specified codepage
     * this translation uses "iconv" and admits texts ONLY in UTF-8.
     *
     * @param  string $text
     * @return string
     */
    protected function translate($text = '')
    {
        if (empty($this->codepage)) {
            $this->defaultCodePage();
        }
        $codep = $this->aCodePage[$this->codepage];
        if (!empty($codep)) {
            $text = @iconv('UTF-8', $codep['conv'], $text);
        }
        return $text;
    }
}
