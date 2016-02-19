<?php

namespace Posprint\Printers;

/**
 * Classe Default das impressoras POS.
 * 
 * A partir dessa classe todas as demais serão estendidas
 * Nas classes "filhas" devem ser inclusos os comando que forem diferentes
 * desses contidos nesta classe basica.
 * 
 * NOTA: Foi construída em torno dos comandos da Epson TM-T20, portanto em teoria 
 * a classe Epson ficará vazia apenas estendendo esta classe.
 * 
 * CodePage default WINDOWS-1250  
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
use Posprint\Connectors\Buffer;
use Exception;

class DefaultPrinter implements PrinterInterface
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
    const SYN = "\x16"; //Sincronismo
    const NOTRANS = false; //not translate characters codepage
    const TRANS = true; //perform a character convertion to codepage
    
    /* Cut types */
    const CUT_FULL = 65;
    const CUT_PARTIAL = 66;
    
     /**
     * List all available countries pages
     * @var array
     */
    public $aCountry = array(
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
     * List all available code pages
     * @var array
     */
    public $aCodePage = array(
        'CP437' => array('conv'=>'437','table'=>'0','desc'=>'PC437: USA, Standard Europe'),
        'CP850' => array('conv'=>'850','table'=>'2','desc'=>'PC850: Multilingual'),
        'CP860' => array('conv'=>'860','table'=>'3','desc'=>'PC860: Portuguese'),
        'CP863' => array('conv'=>'863','table'=>'4','desc'=>'PC863: Canadian-French'),
        'CP865' => array('conv'=>'865','table'=>'5','desc'=>'PC865: Nordic'),
        'CP851' => array('conv'=>'851','table'=>'11','desc'=>'PC851: Greek'),
        'CP853' => array('conv'=>'853','table'=>'12','desc'=>'PC853: Turkish'),
        'CP857' => array('conv'=>'857','table'=>'13','desc'=>'PC857: Turkish'),
        'CP737' => array('conv'=>'737','table'=>'14','desc'=>'PC737: Greek'),
        'ISO8859-7' => array('conv'=>'ISO8859-7','table'=>'15','desc'=>'ISO8859-7: Greek'),
        'CP866' => array('conv'=>'866','table'=>'17','desc'=>'PC866: Cyrillic #2'),
        'CP852' => array('conv'=>'852','table'=>'18','desc'=>'PC852: Latin2'),
        'CP858' => array('conv'=>'858','table'=>'19','desc'=>'PC858: Euro'),
        'CP720' => array('conv'=>'720','table'=>'32','desc'=>'PC720: Arabic'),
        'CP855' => array('conv'=>'855','table'=>'34','desc'=>'PC855: Cyrillic'),
        'CP861' => array('conv'=>'861','table'=>'35','desc'=>'PC861: Icelandic'),
        'CP862' => array('conv'=>'862','table'=>'36','desc'=>'PC862: Hebrew'),
        'CP864' => array('conv'=>'864','table'=>'37','desc'=>'PC864: Arabic'),
        'CP869' => array('conv'=>'869','table'=>'38','desc'=>'PC869: Greek'),
        'ISO8859-2' => array('conv'=>'ISO8859-2','table'=>'39','desc'=>'ISO8859-2: Latin2'),
        'ISO8859-15' => array('conv'=>'ISO8859-15','table'=>'40','desc'=>'ISO8859-15: Latin9'),
        'WINDOWS-1250' => array('conv'=>'WINDOWS-1250','table'=>'45','desc'=>'WPC1250: Latin2'),
        'WINDOWS-1251' => array('conv'=>'WINDOWS-1251','table'=>'46','desc'=>'WPC1251: Cyrillic'),
        'WINDOWS-1252' => array('conv'=>'WINDOWS-1252','table'=>'47','desc'=>'WPC1253: Greek'),
        'WINDOWS-1254' => array('conv'=>'WINDOWS-1254','table'=>'48','desc'=>'WPC1254: Turkish'),
        'WINDOWS-1255' => array('conv'=>'WINDOWS-1255','table'=>'49','desc'=>'WPC1255: Hebrew'),
        'WINDOWS-1256' => array('conv'=>'WINDOWS-1256','table'=>'50','desc'=>'WPC1256: Arabic'),
        'WINDOWS-1257' => array('conv'=>'WINDOWS-1257','table'=>'51','desc'=>'WPC1257: Baltic Rim'),
        'WINDOWS-1258' => array('conv'=>'WINDOWS-1258','table'=>'52','desc'=>'WPC1258: Vietnamese')
    );
    
    /**
     * Resolution in dpi
     * @var int
     */
    public $dpi = 203; //dots per inch
    /**
     * Resolution in dpmm
     * @var int 
     */
    public $dpmm = 8; //dots per mm
    /**
     * Maximum width paper
     * @var int 
     */
    public $widthMaxmm = 80;//mm
    /**
     * Selected Width paper
     * @var int
     */
    public $widthPaper = 80;//mm
    /**
     * Maximum width for printed area
     * @var int
     */
    public $widthPrint = 72;//mm
    /**
     * Maximum width for printed area in dots
     * @var int
     */
    public $widthMaxdots = 576;//dots
    /**
     * Maximum number of characters per line
     * @var int
     */
    public $maxchars = 48;//max characters per line
    
    //protected property standards
    /**
     * Selected internal font
     * @var string
     */
    protected $font = 'A';
    /**
     * Seleted printer mode
     * @var string
     */
    protected $printerMode = 'normal';
    /**
     * Selected Charset Code
     * @var int
     */
    protected $charsetcode = 0;
    /**
     * Seleted code page
     * Defined in printer class
     * @var string
     */
    protected $codepage = 'WINDOWS-1250';
    /**
     * Number of codpage in printer memory
     * @var int
     */
    protected $charsetTableNum = 45;
    /**
     * Selected Country page
     * Defined in printer class
     * @var type 
     */
    protected $country = 'LATIN';
    /**
     * Number of codpage in printer memory
     * @var int
     */
    protected $charsetTableNum = 0;
    /**
     * Selected bold mode
     * @var bool
     */
    protected $boldMode = false;
    /**
     * Selected reverse mode
     * @var bool
     */
    protected $reverseMode = false;
    /**
     * Selected under lined mode
     */
    protected $underlineMode = false;
    /**
     * Buffer class
     * @var Connectors\Buffer
     */
    protected $buffer = null;
    
    /**
     * Method builder
     * Instantiates the data buffer
     */
    public function __construct()
    {
        $this->buffer = new Buffer();
    }
    
    /**
     * Return selected country page
     * or all available countries page for a especific printer 
     * @param bool $all
     * @return string|array
     */
    public function getCountries($all = false)
    {
        if ($all) {
            return $this->aCountry;
        }
        return $this->country;
    }
    
    /**
     * Return selected codepage 
     * or all available code pages
     * @param bool $all
     * @return bool|string|array
     */
    public function getCodePages($all = false, $table = null)
    {
        if (!is_null($table)) {
            $respkey = false;
            foreach ($this->aCodePage as $key => $code) {
                if ($table == $code['table']) {
                    $respkey = $key;
                    break;
                }
            }
            return $respkey;
        }
        $keys = array_keys($this->aCodePage);
        if ($all) {
            return $keys;
        }
        return $this->codepage;
    }
    
    /**
     * Send message or command to buffer
     * when sending commands is not required to convert characters,
     * so the variable may translate by false
     * 
     * @param string $text
     * @param bool $translate
     */
    public function text($text = '', $translate = true)
    {
        if ($translate) {
            $text = $this->zTranslate($text);
        }    
        $this->buffer->write($text);
    }
    
    
    /**
     * Sends a separator line to buffer
     */
    public function line()
    {
        $text = str_repeat('-', $this->maxchars);
        $this->text($text);
    }
    
    /**
     * Close and clean buffer
     * All data will be lost
     */
    public function close()
    {
        $this->buffer->close();
    }
    
    /**
     * Return all data buffer 
     * @param string $type specifies the return format
     */
    public function send($type = '')
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
            case 'readS':
                //returns a human readable format of string buffer
                //only for debug reasons
                $resp = $this->buffer->getDataReadable(false);
                break;
            default :
                $resp = $this->buffer->getDataReadable(false);
        }
        return $resp;
    }
    
    /**
     * Calculate the size of the word
     * @param string $data
     */
    protected function getWordLength($data = '')
    {
        //k = (pL + pH × 256) – 3
        $len = strlen($texto);
    }

   /**
     * Adjust Paper Width
     * this adjustment has implications on the printable area and other printing details
     * @param int $width
     */
    public function setPaperWidth($width = 80)
    {
        // 72.1 mm (2.84"), 576 dots 52.6 mm (2.07"), 420 dots
        //Normal mode (initial setting)
        //                  80mm         58mm
        // Font A (12 x 24) 48 chars     35 chars
        // Font B (9 x 17)  64 chars     46 chars
        //42 column mode
        // Font A (13 x 24) 42           42
        // Font B (9 x 17)  60           31
        if ($width != 80) {
            $width = 58;
        }
        $this->widthPaper = $width;
        $this->zSetMaxValues();
    }
    
    /**
     * Set horizontal and vertical motion units
     * $horizontal => character spacing 1/x"
     * $vertical => line spacing 1/y"
     * @param int $horizontal
     * @param int $vertical
     */
    public function setSpacing($horizontal = 30, $vertical = 30)
    {
        $this->buffer->write(self::GS . "P" . chr($horizontal) . chr($vertical));
    }
    
    /**
     * Set right-side character spacing
     * 0 ≤ n ≤ 255 => 1/x"
     * 
     * @param int $value
     */
    public function setCharSpacing($value = 3)
    {
        $this->buffer->write(self::ESC . " " . chr($value));
    }
    
    /**
     * Line spacing
     * The default is set to zero and 30/180 "
     * any different number of zero will generate multiples of
     * @param int $paragraph
     */
    public function setParagraph($value = 0)
    {   //n * 1/180-inch vertical motion
        //normal paragrafo 30/180" => 4.23 mm
        $paragraph = ceil($value);
        if ($paragraph == 0) {
            $this->buffer->write(self::ESC . "2");
            return;
        }
        if ($paragraph < 25) {
            $paragraph = 25;
        } elseif ($paragraph > 255) {
            $paragraph = 255;
        }
        $this->buffer->write(self::ESC . "3" . chr($paragraph));
    }
    
    /**
     * Set the printer mode
     */
    public function setPrintMode($mode = null)
    {
        //not set for this printer
    }        
    
    /**
     * setFont
     * Seleciona a fonte interna a ser usada 
     * A - Font A (12 x 24)
     * B - Font B (9 x 17)
     * Default Font A
     * @param string $font
     */
    public function setFont($font = 'A')
    {
        $num = 0;
        if ($font != 'A') {
            $num = 1;
        }
        $this->buffer->write(self::ESC . "M" . chr($num));
        //salva os parametros máximos de largura para a fonte escolhida
        $this->zSaveMaxParams();
    }
    
    /**
     * Select a code page 
     * @param int $table
     */
    public function setCharset($tableNum = 45)
    {
        $this->codepage = $this->getCodePages('', $tableNum);
        $this->charsetTableNum = $tableNum;
        $this->buffer->write(self::ESC . "t" . chr($tableNum));
    }
    /**
     * Selects a country page
     * @param string $country
     */
    public function setInternational($country = 'LATIN')
    {
        $mode = array_keys($this->aCountry, $country, true);
        $this->buffer->write(self::ESC . "R" . chr($mode));
    }
    
    /**
     * Set emphasys mode on or off
     */
    public function setBold()
    {
        if ($this->boldMode) {
            $this->boldMode = false;
        } else {
            $this->boldMode = true;
        }
        $this->buffer->write(self::ESC . "E". ($this->boldMode ? chr(1) : chr(0)));
    }    

    /**
     * Set underline mode on or off
     */
    public function setUnderlined($active = false)
    {
        if ($this->underlineMode) {
            $this->underlineMode = false;
        } else {
            $this->underlineMode = true;
        }
        $this->buffer->write(self::ESC . "-". ($this->underlineMode ? chr(1) : chr(0)));
    }       

    /**
     * Set expanded mode
     */
    public function setExpanded($doubleH = false, $doubleW = false)
    {
        $mode = 0;
        ($doubleH) ? $mode += 16 : $mode += 0;
        ($doubleW) ? $mode += 32 : $mode += 0;
        $this->buffer->write(self::ESC . "!" . chr($mode));
    }
    
    /**
     * Set condensed mode
     */
    public function setCondensed()
    {
        $this->setFont('B');
    }      

    /**
     * Set rotate 90 degrees
     * @param bool $active
     */
    public function setRotate90($active = false)
    {
        ($active) ? $mode = 1: $mode = 0;
        $this->buffer->write(self::ESC . "V" . chr($mode));
    }
         
    /**
     * Turns white/black reverse print On or Off for characters.
     * n = odd: On, n = even: Off
     */        
    public function setReverseColors()
    {
        $mode = 1;
        if ($this->reverseMode) {
            $mode = 2;
            $this->reverseMode = true;
        }
        $this->buffer->write(self::GS . "b" . chr($mode));
    }
    
    /**
     * Aligns all data in one line to the selected layout in standard mode.
     * @param string $value  L - left C - center or R - rigth
     */
    public function setJustification($value = 'L')
    {
        //ESC a n
        //Aligns all data in one line to the selected layout in
        //standard mode.
        //n = 0, "0": Left justification
        //n = 1, "1": Centering
        //n = 2, "2": Right justification
        $value = strtoupper($value);
        switch ($value) {
            case 'C':
               $nJust = 1;
               break;
            case 'R':
               $nJust = 2;
               break;
            default:
                $nJust = 0;
        }
        $this->buffer->write(self::ESC . "a" . chr($nJust));
    }
           
    /**
     * initialize
     * Inicializa a impressora
     * Clears the data in the print buffer and resets the printer modes to 
     * the modes that were in effect when the power was turned on
     * 
     * @param string $mode 'normal' ou '42' colunas
     */
    public function initialize($mode = 'normal')
    {
        $this->buffer->write(self::ESC . "@");
        $this->characterTable = 0;
        if ($mode == '42') {
            $this->zSetTo42Col();
        }
    }      

    /**
     * Prints data and feeds paper n lines
     * ESC J n Prints data and feeds paper n dots.
     * ESC d n Prints data and feeds paper n lines.
     * @param type $lines
     */
    public function lineFeed($lines = 1)
    {
        if ($lines <= 1) {
            $this->buffer->write(self::LF);
        } else {
            $this->buffer->write(self::ESC . "d" . chr($lines));
        }
    }    

    /**
     * Prints data and feeds paper n dots
     * ESC J n Prints data and feeds paper n dots.
     * @param type $lines
     */
    public function dotFeed($dots = 1)
    {
        $this->buffer->write(self::ESC . "J" . chr($dots));
    }    
    
    /**
     * Generate a pulse, for opening a cash drawer if one is connected.
     * The default settings should open an Epson drawer.
     *
     * @param int $pin 0 or 1, for pin 2 or pin 5 kick-out connector respectively.
     * @param int $on_ms pulse ON time, in milliseconds.
     * @param int $off_ms pulse OFF time, in milliseconds.
     */
    public function pulse($pin = 0, $on_ms = 120, $off_ms = 240)
    {
        self::validateInteger($pin, 0, 1, __FUNCTION__);
        self::validateInteger($on_ms, 1, 511, __FUNCTION__);
        self::validateInteger($off_ms, 1, 511, __FUNCTION__);
        $this->buffer->write(self::ESC . "p" . chr($pin + 48) . chr($on_ms / 2) . chr($off_ms / 2));
    }   
            
    /**
     * Cut the paper
     * @param int $mode CUT_FULL or CUT_PARTIAL. If not specified, CUT_FULL will be used. 
     * @param int $lines Number of lines to feed after cut
     */
    public function cut($mode = 65, $lines = 3)
    {
        $this->buffer->write(self::GS . "V" . chr($mode) . chr($lines));
    }
    
    /**
     * Prints EAN13 barcode
     * @param int $height
     * @param int $lineWidth
     * @param string $txtPosition
     * @param string $txtFont
     * @param string $data
     */
    public function barcodeEAN13(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456789012'
    ) {
        $type = 2;
        $this->zBarcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }     
    
    /**
     * Prints EAN8 barcode
     * @param int $height
     * @param int $lineWidth
     * @param string $txtPosition
     * @param string $txtFont
     * @param string $data
     */
    public function barcodeEAN8(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '1234567'
    ) {
        $type = 3;
        $this->zBarcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
         
    /**
     * Prints 25 barcode
     * @param int $height
     * @param int $lineWidth
     * @param string $txtPosition
     * @param string $txtFont
     * @param string $data
     */
    public function barcode25(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        $type = 'none';
        $this->zBarcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }

    /**
     * Prints 39 barcode
     * @param int $height
     * @param int $lineWidth
     * @param string $txtPosition
     * @param string $txtFont
     * @param string $data
     */
    public function barcode39(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = 0,
        $txtFont = 0,
        $data = '123456'
    ) {
        $type = 4;
        $this->zBarcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }
            
    /**
     * Prints 93 barcode
     * @param int $height
     * @param int $lineWidth
     * @param string $txtPosition
     * @param string $txtFont
     * @param string $data
     */
    public function barcode93(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        $type = 'none';
        $this->zBarcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }       
            
    /**
     * Prints 128 barcode
     * @param int $height
     * @param int $lineWidth
     * @param string $txtPosition
     * @param string $txtFont
     * @param string $data
     */
    public function barcode128(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        $type = 'none';
        $this->zBarcode($type, $height, $lineWidth, $txtPosition, $txtFont, $data);
    }      
    
    /**
     * Prints QR barcode
     * @param string $texto
     * @param string $level
     * @param string $modelo
     * @param string $wmod
     */
    public function barcodeQRCode($texto = '', $level = 'L', $modelo = '1', $wmod = 1)
    {
        $b2dtype = chr(49);
        $pLower = chr(3);
        $pHigher = chr(0);
        $errlevel = chr(48);
        //set QRCode model
        $func = chr(65);
        $qrmod = 49;
        if ($modelo == 2) {
            $qrmod = 50;
        }
        $this->buffer->write(self::GS . "(k" . $pLower . $pHigher . $b2dtype . $func . $qrmod . chr(0));
        //set module size in dots
        $func = chr(67);
        $this->buffer->write(self::GS . "(k" . $pLower . $pHigher . $b2dtype . $func . $wmod);
        //set error correction level
        $func = chr(69);
        $this->buffer->write(self::GS . "(k" . $pLower . $pHigher . $b2dtype . $func . $errlevel);
        //print QR Code
        //k = (pL + pH × 256) – 3
        $func = chr(80);
        $metodo = chr(48);
        $this->buffer->write(self::GS . "(k" . $pLower . $pHigher . $b2dtype . $func . $metodo . $texto);
    }    
            
    public function barcodePdf417()
    {
        
    }        
            
    public function putImage()
    {
        
    }
    
    /**
     * Implements barcodes
     * @param int $type
     * @param int $height
     * @param int $lineWidth
     * @param string $txtPosition
     * @param string $txtFont
     * @param string $data
     */
    protected function zBarcode(
        $type = 0,
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456'
    ) {
        if ($type != 'none') {
            $this->buffer->write(self::GS . "h" . chr($height));
            $this->buffer->write(self::GS . 'w' . chr($lineWidth));
            $this->buffer->write(self::GS . 'H' . chr($txtPosition));
            $this->buffer->write(self::GS . 'f' . chr($txtFont));
            $this->buffer->write(self::GS . "k" . chr($type) . $data . self::NUL);
        }
    }    

    /**
     * Translate the text from UTF-8 for the specified codepage
     * this translation uses "iconv" and admits texts ONLY in UTF-8
     * @param string $text
     * @return string
     */
    private function zTranslate($text = '')
    {
        $indCode = $this->getCodePages();
        if (! empty($indCode)) {
            $codep = $this->aCodePage[$indCode];
            if (! empty($codep)) {
                $text = iconv('UTF-8', $codep['conv'], $text);
            }
        }
        return $text;
    }

    /**
     * Salva os parametros referentes a larguras maximas 
     * referentes a impresção para uso em outros métodos
     */
    private function zSaveMaxParams()
    {
        $aMv = array(
            'normal' => array(
                'A'=> array('80'=>48,'58'=>35),
                'B'=> array('80'=>64,'58'=>46)),
            '42' => array(
                'A'=> array('80'=>42,'58'=>42),
                'B'=> array('80'=>60,'58'=>31))
        );
        //80mm -> wprintmax = 72.1 mm (2.84"), 576 dots
        //58mm -> wprintmax = 52.6 mm (2.07"), 420 dots
        // normal
        // Paper width        80mm         58mm
        // Font A (12 x 24) 48 chars     35 chars
        // Font B (9 x 17)  64 chars     46 chars
        //-------------------------------------------
        // 42 Columns $this->printerMode
        // Font A (13 x 24) 42 chars     42 chars
        // Font B (9 x 17)  60 chars     31 chars
        $widthPrint = 72.1;
        $this->widthMaxdots = 576;
        if ($this->widthPaper != 80) {
            $widthPrint = 52.6;
            $this->widthMaxdots = 420;
        }
        $this->widthPrint = $widthPrint;
        $this->maxchars = $aMv[$this->printerMode][$this->font][$this->widthPaper];
    }
    
    /**
     * Throw an exception if the argument given is not a boolean
     * @param boolean $test the input to test
     * @param string $source the name of the function calling this
     */
    protected static function validateBoolean($test, $source)
    {
        if (!($test === true || $test === false)) {
            throw new InvalidArgumentException("O argumento para $source deve ser um booleano");
        }
    }
    
    /**
     * Throw an exception if the argument given is not an integer within the specified range
     * 
     * @param int $test the input to test
     * @param int $min the minimum allowable value (inclusive)
     * @param int $max the maximum allowable value (inclusive)
     * @param string $source the name of the function calling this
     */
    protected static function validateInteger($test, $min, $max, $source)
    {
        if (!is_integer($test) || $test < $min || $test > $max) {
            throw new InvalidArgumentException("O argumento para $source deve ser um numero entre $min e $max, mas $test foi fornecido.");
        }
    }
    
    /**
     * Throw an exception if the argument given can't be cast to a string
     * @param string $test the input to test
     * @param string $source the name of the function calling this
     */
    protected static function validateString($test, $source)
    {
        if (is_object($test) && !method_exists($test, '__toString')) {
            throw new InvalidArgumentException("O argumento para $source deve ser uma string");
        }
    }    
}