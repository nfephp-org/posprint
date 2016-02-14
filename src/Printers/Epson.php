<?php

namespace Posprint\Printers;

/**
 * Classe Epson das impressoras POS.
 * 
 * Foi construída em torno dos comandos da Epson TM-T20
 * Velocidade Máxima: 150 mm/s (5,91 pol/s)
 * Resolução: 203 dpi
 * Largura de Impressão: Papel 80 mm (Máx. 72 mm) 48col/64col;
 * Largura de Impressão: Papel 58 mm (Máx. 54 mm) 35col/46col;
 * 
 * CodePage default WINDOWS-1250  
 * CountyPage default LATIN
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

class Epson extends Printer implements PrinterInterface
{
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
     * Send message or command to buffer
     * @param string $text
     */
    public function text($text = '')
    {
        $indCode = $this->getCodePages();
        $codep = $this->aCodePage[$indCode];
        $ntext = iconv('UTF-8', $codep['conv'], $text);
        parent::text($ntext);
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
     * This command does not exist for this printer
     */
    public function setMargins($left = 0, $right = 0)
    {
        return '';
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
        $this->text(self::GS . "P" . chr($horizontal) . chr($vertical));
    }

    /**
     * Set right-side character spacing
     * 0 ≤ n ≤ 255 => 1/x"
     * 
     * @param int $value
     */
    public function setCharSpacing($value = 3)
    {
        $this->text(self::ESC . " " . chr($value));
    }
    
    /**
     * Selects the character font and styles 
     * (emphasized, double-height, double-width, and underline) together
     * @param type $mode
     */
    public function setPrintMode(
        $font = 'A',
        $bold = false,
        $doubleH = false,
        $doubleW = false,
        $underline = false
    ) {
        ($font == 'A') ? $mode = 0: $mode = 1;
        if ($mode == 1) {
            $this->font = 'B';
        }
        ($bold) ? $mode += 8 : $mode += 0;
        ($doubleH) ? $mode += 16 : $mode += 0;
        ($doubleW) ? $mode += 32 : $mode += 0;
        ($underline) ? $mode += 128 : $mode += 0;
        $this->text(self::ESC . "!" . chr($mode));
    }
    
    /**
     * setFont
     * Seleciona a fonte interna a ser usada 
     * A - Font A (12 x 24)
     * B - Font B (9 x 17)
     * Default Font A
     * @param type $font
     */
    public function setFont($font = 'A')
    {
        $num = 0;
        if ($font != 'A') {
            $num = 1;
        }
        $this->text(self::ESC . "M" . chr($num));
        $this->zSetMaxValues();
    }
    
    /**
     * Select a code page 
     * @param int $table
     */
    public function setCharset($tableNum = 45)
    {
        $this->codepage = $this->getCodePages('', $tableNum);
        $this->charsetTableNum = $tableNum;
        $this->text(self::ESC . "t" . chr($tableNum));
    }
    
    /**
     * Selects a country page
     * @param string $country
     */
    public function setInternational($country = 'LATIN')
    {
        $mode = array_keys($this->aCountry, $country, true);
        $this->text(self::ESC . "R" . chr($mode));
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
        $this->text(self::ESC . "E". ($this->boldMode ? chr(1) : chr(0)));
    }
    
    /**
     * This command is not available for this printer
     */
    public function setItalic()
    {
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
        $this->text(self::ESC . "-". ($this->underlineMode ? chr(1) : chr(0)));
    }
    
    /**
     * Set expanded mode
     */
    public function setExpanded($doubleH = false, $doubleW = false)
    {
        $mode = 0;
        ($doubleH) ? $mode += 16 : $mode += 0;
        ($doubleW) ? $mode += 32 : $mode += 0;
        $this->text(self::ESC . "!" . chr($mode));
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
        $this->text(self::ESC . "V" . chr($mode));
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
            $this->text(self::ESC . "2");
            return;
        }
        if ($paragraph < 25) {
            $paragraph = 25;
        } elseif ($paragraph > 255) {
            $paragraph = 255;
        }
        $this->text(self::ESC . "3" . chr($paragraph));
    }
    
    /**
     * 
     */
    public function setReverseColors()
    {
        
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
        $this->text(self::ESC . "a" . chr($nJust));
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
        $this->text(self::ESC . "@");
        $this->characterTable = 0;
        if ($mode == '42') {
            $this->zSetTo42Col();
        }
    }

    
    /**
     * 
     * @param type $height
     * @param type $lineWidth
     * @param type $txtPosition
     * @param type $txtFont
     * @param type $data
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
     * 
     * @param type $height
     * @param type $lineWidth
     * @param type $txtPosition
     * @param type $txtFont
     * @param type $data
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
     * 
     * @param type $height
     * @param type $lineWidth
     * @param type $txtPosition
     * @param type $txtFont
     * @param type $data
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
     * 
     * @param type $height
     * @param type $lineWidth
     * @param type $txtPosition
     * @param type $txtFont
     * @param type $data
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
     * 
     * @param type $height
     * @param type $lineWidth
     * @param type $txtPosition
     * @param type $txtFont
     * @param type $data
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
     * 
     * @param type $height
     * @param type $lineWidth
     * @param type $txtPosition
     * @param type $txtFont
     * @param type $data
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
     * 
     * @param type $type
     * @param type $height
     * @param type $lineWidth
     * @param type $txtPosition
     * @param type $txtFont
     * @param type $data
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
            $this->text(self::GS . "h" . chr($height));
            $this->text(self::GS . 'w' . chr($lineWidth));
            $this->text(self::GS . 'H' . chr($txtPosition));
            $this->text(self::GS . 'f' . chr($txtFont));
            $this->text(self::GS . "k" . chr($type) . $data . self::NUL);
        }
    }

    /**
     * 
     */
    public function barcodeQRCode($texto = '', $level = 'L', $modelo = '1', $wmod = 1)
    {
        //GS ( k pL pH cn fn [parameters]
        //Stores, prints symbol data, or configure the settings.
        //cn
        //48: PDF417
        //49: QR Code
        //50: MaxiCode
        //51: 2-dimensional GS1 Dat
        
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
        $this->text(self::GS . "(k" . $pLower . $pHigher . $b2dtype . $func . $qrmod . chr(0));
        
        //set module size in dots
        $func = chr(67);
        $this->text(self::GS . "(k" . $pLower . $pHigher . $b2dtype . $func . $wmod);

        //set error correction level
        $func = chr(69);
        $this->text(self::GS . "(k" . $pLower . $pHigher . $b2dtype . $func . $errlevel);
        
        //print QR Code
        //k = (pL + pH × 256) – 3
        $func = chr(80);
        $metodo = chr(48);
        $this->text(self::GS . "(k" . $pLower . $pHigher . $b2dtype . $func . $metodo . $texto);
        
    }
    
    /**
     * 
     */
    public function barcodePdf417()
    {
        
    }
    
    /**
     * 
     * @param type $lines
     */
    public function feed($lines = 1)
    {
        if ($lines <= 1) {
            $this->text(self::LF);
        } else {
            $this->text(self::ESC . "d" . chr($lines));
        }
    }
    
    /**
     * 
     * @param type $lines
     */
    public function feedReverse($lines = 1)
    {
        if ($lines > 255) {
            $lines = 255;
        }
        $this->text(self::ESC . "e" . chr($lines));
    }
    
    /**
     * 
     * @param type $pin
     * @param type $onMs
     * @param type $offMs
     */
    public function pulse($pin = 0, $onMs = 120, $offMs = 240)
    {
        $this->connector->write(self::ESC . "p" . chr($pin + 48) . chr($onMs / 2) . chr($offMs / 2));
    }
    
    public function putImage()
    {
        
    }
    
    /**
     * 
     * @param type $mode
     * @param type $lines
     */
    public function cut($mode = 65, $lines = 3)
    {
        $this->text(self::GS . "V" . chr($mode) . chr($lines));
    }
    
        
    private function zSetMaxValues()
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
    
    private function zSetTo42Col()
    {
        //Veja abaixo as seqüências de comandos ESC/POS (comunicação direta)
        // para configurar o modo de impressão (padrão ou 42 colunas):
        // Entra no modo de configuração avançado da impressora
        //029 040 069 003 000 001 073 078
        //GS   (   E   pL pH  fn   d1   d2  Change into the user setting mode
        $this->text(self::GS.'(E'.chr(3).chr(0).chr(1).chr(73).chr(78));
        // Configura o modo de emulação de colunas na impressora
        //029 040 069 004 000 005 011 001 000
        //GS   (   E   pL pH  fn   d1  d2  d3  Set the customized setting values
        $this->text(self::GS.'(E'.chr(4).chr(0).chr(5).chr(11).chr(1).chr(0));
        //Os dois últimos bytes do comando acima, definem o modo de emulação
        //de colunas devendo seguir à seguinte regra:
        //000 000 - Modo Normal (Configuração Default)
        //001 000 – Modo de 42 Colunas
        // Sai e finaliza o modo de configuração avançado da impressora
        //029 040 069 004 000 002 079 085 084
        //GS   (   E   pL pH  fn  d1  d2  d3  End the user setting mode session
        $this->text(self::GS.'(E'.chr(4).chr(0).chr(2).chr(79).chr(85).chr(84));
        //Obs.: esta configuração do modo de impressão ficará gravada na impressora,
        //portanto o comando não precisa necessariamente ser executado mais de uma vez.
        $this->printerMode = '42';
    }
}
