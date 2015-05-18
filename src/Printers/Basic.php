<?php

namespace Posprint\Printers;

/**
 * Classe base das impressoras POS.
 * Todas as demais classes de impressoras são extensões desta classe.
 * Implementa uma inteface que estabelece uma série de comandos comuns entre as
 * várias marcas e modleo de impressoras POS termicas.
 * 
 * IMPORTANTE: Alguns comandos podem não existir para uma determinada impressora
 * ou não funcionar devido a diferenças em seu firmware, mesmo entre impressoras
 * de mesmo modelo e marca. Portanto é importante garantir um firmware atualizado.
 * 
 * Foi construída em torno dos comandos da Epson TM-T20
 * 
 * @category   API
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Printers\Printer;
use Posprint\Connectors;
use Posprint\Common\Graphics;

class Basic implements Printer
{
   
    const NUL = "\x0"; //Nulo
    const EOT = "\x4"; //EOT fim da transmissão
    const ENQ = "\x5"; //ENQ colocar na fila
    const HT = "\x9"; //tabulação horizontal
    const LF = "\x0a"; //avança linha
    const FF = "\x0c"; //avança pagina
    const CR = "\x0d"; //retorno de carro
    const DLE = "\x10"; //Data Link Escape
    const DC4 = "\x14"; //DC4 Controle de dispositivo 4
    const CAN = "\x18"; //CAN
    const ESC = "\x1b"; //escape
    const FS = "\x1c"; //FS
    const GS = "\x1d"; //GS
    
    protected $connector;
    protected $charsetTableNum = 0;
    protected $font = 'A';
    protected $printerMode = 'normal';
    
    public $dpi = 203;
    public $dpmm = 8;
    public $widthMaxmm = 80;//mm
    public $widthPaper = 80;//mm
    public $widthPrint = 72;//

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
    
    public $aCodePage = array(
        array('id'=>'CP437','conv'=>'437','table'=>'0','desc'=>'PC437: USA, Standard Europe'),
        array('id'=>'Katakana','conv'=>'none','table'=>'1','desc'=>'Katakana'),
        array('id'=>'CP850','conv'=>'850','table'=>'2','desc'=>'PC850: Multilingual'),
        array('id'=>'CP860','conv'=>'860','table'=>'3','desc'=>'PC860: Portuguese'),
        array('id'=>'CP863','conv'=>'863','table'=>'4','desc'=>'PC863: Canadian-French'),
        array('id'=>'CP865','conv'=>'865','table'=>'5','desc'=>'PC865: Nordic'),
        array('id'=>'CP851','conv'=>'851','table'=>'11','desc'=>'PC851: Greek'),
        array('id'=>'CP853','conv'=>'853','table'=>'12','desc'=>'PC853: Turkish'),
        array('id'=>'CP857','conv'=>'857','table'=>'13','desc'=>'PC857: Turkish'),
        array('id'=>'CP737','conv'=>'737','table'=>'14','desc'=>'PC737: Greek'),
        array('id'=>'ISO8859-7','conv'=>'ISO8859-7','table'=>'15','desc'=>'ISO8859-7: Greek'),
        array('id'=>'WINDOWS-1252','conv'=>'WINDOWS-1252','table'=>'16','desc'=>'WPC1252'),
        array('id'=>'CP866','conv'=>'866','table'=>'17','desc'=>'PC866: Cyrillic #2'),
        array('id'=>'CP852','conv'=>'852','table'=>'18','desc'=>'PC852: Latin2'),
        array('id'=>'CP858','conv'=>'858','table'=>'19','desc'=>'PC858: Euro'),
        array('id'=>'KU42','conv'=>'none','table'=>'20','desc'=>'KU42: Thai'),
        array('id'=>'TIS11i','conv'=>'none','table'=>'21','desc'=>'TIS11: Thai'),
        array('id'=>'TIS18','conv'=>'none','table'=>'26','desc'=>'TIS18: Thai'),
        array('id'=>'TCVN-3','conv'=>'none','table'=>'30','desc'=>'TCVN-3: Vietnamese'),
        array('id'=>'TCVN-3','conv'=>'none','table'=>'31','desc'=>'TCVN-3: Vietnamese'),
        array('id'=>'CP720','conv'=>'720','table'=>'32','desc'=>'PC720: Arabic'),
        array('id'=>'WINDOWS-775','conv'=>'none','table'=>'33','desc'=>'WPC775: Baltic Rim'),
        array('id'=>'CP855','conv'=>'855','table'=>'34','desc'=>'PC855: Cyrillic'),
        array('id'=>'CP861','conv'=>'861','table'=>'35','desc'=>'PC861: Icelandic'),
        array('id'=>'CP862','conv'=>'862','table'=>'36','desc'=>'PC862: Hebrew'),
        array('id'=>'CP864','conv'=>'864','table'=>'37','desc'=>'PC864: Arabic'),
        array('id'=>'CP869','conv'=>'869','table'=>'38','desc'=>'PC869: Greek'),
        array('id'=>'ISO8859-2','conv'=>'ISO8859-2','table'=>'39','desc'=>'ISO8859-2: Latin2'),
        array('id'=>'ISO8859-15','conv'=>'ISO8859-15','table'=>'40','desc'=>'ISO8859-15: Latin9'),
        array('id'=>'CP1098','conv'=>'none','table'=>'41','desc'=>'PC1098: Farsi'),
        array('id'=>'CP1118','conv'=>'none','table'=>'42','desc'=>'PC1118: Lithuanian'),
        array('id'=>'CP1119','conv'=>'none','table'=>'43','desc'=>'PC1119: Lithuanian'),
        array('id'=>'CP1125','conv'=>'none','table'=>'44','desc'=>'PC1125: Ukrainian'),
        array('id'=>'WINDOWS-1250','conv'=>' WINDOWS-1250','table'=>'45','desc'=>'WPC1250: Latin2'),
        array('id'=>'WINDOWS-1251','conv'=>' WINDOWS-1251','table'=>'46','desc'=>'WPC1251: Cyrillic'),
        array('id'=>'WINDOWS-1252','conv'=>' WINDOWS-1252','table'=>'47','desc'=>'WPC1253: Greek'),
        array('id'=>'WINDOWS-1254','conv'=>' WINDOWS-1254','table'=>'48','desc'=>'WPC1254: Turkish'),
        array('id'=>'WINDOWS-1255','conv'=>' WINDOWS-1255','table'=>'49','desc'=>'WPC1255: Hebrew'),
        array('id'=>'WINDOWS-1256','conv'=>' WINDOWS-1256','table'=>'50','desc'=>'WPC1256: Arabic'),
        array('id'=>'WINDOWS-1257','conv'=>' WINDOWS-1257','table'=>'51','desc'=>'WPC1257: Baltic Rim'),
        array('id'=>'WINDOWS-1258','conv'=>' WINDOWS-1258','table'=>'52','desc'=>'WPC1258: Vietnamese'),
        array('id'=>'KZ-1048','conv'=>'none','table'=>'53','desc'=>'KZ-1048: Kazakhstan'),
        array('id'=>'user','conv'=>'none','table'=>'255','desc'=>'User-defined page')
    );
    
    
    /**
     * 
     * @param type $connector
     */
    public function __construct($connector = null, $bufferize = true)
    {
        if (isNull($connector) || $bufferize) {
            $this->connector = new Connectors\Buffer();
        }
    }
    
    /**
     * 
     */
    public function setDevice()
    {
        
    }
    
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

        $widthMax = 72.1;
        if ($width != 80) {
            $width = 58;
            $widthMax = 52.6;
        }
        $this->widthPaper = $width;
        $this->zSetMaxValues();
    }
    
    private function zSetMaxValues()
    {
        //80mm -> wprintmax = 72.1 mm (2.84"), 576 dots
        //58mm -> wprintmax = 52.6 mm (2.07"), 420 dots
        
        // normal
        // Paper width        80mm         58mm
        // Font A (12 x 24) 48 chars     35 chars
        // Font B (9 x 17)  64 chars     46 chars
        
        // 42 Columns $this->printerMode
        // Font A (13 x 24) 42 chars     42 chars
        // Font B (9 x 17)  60 chars     31 chars
    }
    
    /**
     * 
     */
    public function setMargins()
    {
        
    }
    
    /**
     * Set horizontal and vertical motion units
     * $horizontal => entre caracteres 1/x"
     * $vertical => entre linhas 1/y"
     * 
     * @param type $horizontal
     * @param type $vertical
     */
    public function setSpacing($horizontal = 30, $vertical = 30)
    {
        $this->connector->write(self::GS . "P" . chr($horizontal) . chr($vertical));
    }

    /**
     * Set right-side character spacing
     * 0 ≤ n ≤ 255 => 1/x"
     * 
     * @param int $value
     */
    public function setCharSpacing($value = 3)
    {
        $this->connector->write(self::ESC . " " . chr($value));
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
        ($bold) ? $mode = 8 : $mode = 0;
        ($doubleH) ? $mode += 16 : $mode += 0;
        ($doubleW) ? $mode += 32 : $mode += 0;
        ($underline) ? $mode += 128 : $mode += 0;
        $this->connector->write(self::ESC . "!" . chr($mode));
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
        $this->connector->write(self::ESC . "M" . chr($num));
        $this->zSetMaxValues();
    }
    
    /**
     * 
     * @param type $table
     */
    public function setCharset($tableNum = 0)
    {
        $this->connector->write(self::ESC . "t" . chr($tableNum));
        $this->charsetTableNum = $tableNum;
    }
    
    public function setInternational($country = 'LATIN')
    {
        $mode = array_keys($this->aCountry, $country, true);
        $this->connector->write(self::ESC . "R" . chr($mode));
    }
    
    /**
     * 
     * @param type $active
     */
    public function setBold($active = true)
    {
        $this->connector->write(self::ESC . "E". ($active ? chr(1) : chr(0)));
    }
    
    /**
     * 
     */
    public function setItalic($active = false)
    {
        $active = true;
        //não faz nada não tem essa opção
    }
    
    /**
     * 
     * @param type $underline
     */
    public function setUnderlined($active = false)
    {
        ($active) ? $mode = 1 : $mode = 0;
        $this->connector->write(self::ESC . "-". chr($mode));
    }
    
    /**
     * 
     */
    public function setExpanded($doubleH = false, $doubleW = false)
    {
        $mode = 0;
        ($doubleH) ? $mode += 16 : $mode += 0;
        ($doubleW) ? $mode += 32 : $mode += 0;
        $this->connector->write(self::ESC . "!" . chr($mode));
    }
    
    /**
     * 
     */
    public function setCondensed()
    {
        $this->setFont('B');
    }
    
    public function setRotate90($active = false)
    {
        ($active) ? $mode = 1: $mode = 0;
        $this->connector->write(self::ESC . "V" . chr($mode));
    }
    
    /**
     * Espaçamento entre linhas
     * O padrão é estabelecido com zero e é 30/180"
     * qualuqer numero diferente de zero irá gerar multiplos de 
     * 
     * @param type $paragrafo
     */
    public function setParagraf($paragrafo = 0)
    {   //n * 1/180-inch vertical motion
        //normal paragrafo 30/180" => 4.23 mm
        $paragrafo = round((int) $paragrafo);
        if ($paragrafo == 0) {
            $this->connector->write(self::ESC . "2");
            return;
        }
        if ($paragrafo < 25) {
            $paragrafo = 25;
        } elseif ($paragrafo > 255) {
            $paragrafo = 255;
        }
        $this->connector->write(self::ESC . "3" . chr($paragrafo));
    }
    
    /**
     * 
     */
    public function setReverseColors()
    {
        
    }
    
    /**
     * 
     * @param type $justification
     */
    public function setJustification($justification)
    {
        $this->connector->write(self::ESC . "a" . chr($justification));
    }
    
    /**
     * initialize
     * Inicializa a impressora
     * Clears the data in the print buffer and resets the printer modes to 
     * the modes that were in effect when the power was turned on
     * 
     * @param string $mode 'normal' ou 42 colunas
     */
    public function initialize($mode = 'normal')
    {
        $this->connector->write(self::ESC . "@");
        $this->characterTable = 0;
        if ($mode != 'normal') {
            $this->zSetTo42Col();
        }
    }
    
    private function zSetTo42Col()
    {
        //Veja abaixo as seqüências de comandos ESC/POS (comunicação direta)
        // para configurar o modo de impressão (padrão ou 42 colunas):
        // Entra no modo de configuração avançado da impressora
        //029 040 069 003 000 001 073 078
        //GS   (   E   pL pH  fn   d1   d2  Change into the user setting mode
        $this->connector->write(self::GS.'(E'.chr(3).chr(0).chr(1).chr(73).chr(78));
        // Configura o modo de emulação de colunas na impressora
        //029 040 069 004 000 005 011 001 000
        //GS   (   E   pL pH  fn   d1  d2  d3  Set the customized setting values
        $this->connector->write(self::GS.'(E'.chr(4).chr(0).chr(5).chr(11).chr(1).chr(0));
        //Os dois últimos bytes do comando acima, definem o modo de emulação
        //de colunas devendo seguir à seguinte regra:
        //000 000 - Modo Normal (Configuração Default)
        //001 000 – Modo de 42 Colunas
        // Sai e finaliza o modo de configuração avançado da impressora
        //029 040 069 004 000 002 079 085 084
        //GS   (   E   pL pH  fn  d1  d2  d3  End the user setting mode session
        $this->connector->write(self::GS.'(E'.chr(4).chr(0).chr(2).chr(79).chr(85).chr(84));
        //Obs.: esta configuração do modo de impressão ficará gravada na impressora,
        //portanto o comando não precisa necessariamente ser executado mais de uma vez.
        $this->printerMode = '42';
    }
    
    /**
     * 
     * @param type $text
     */
    public function text($text = '')
    {
        $this->connector->write($text);
    }
    
    /**
     * 
     */
    public function line()
    {
        $text = '----------------------------------------';
        $this->connector->write($text);
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
    public function barcodeUPCA(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456789012'
    ) {
        $type = 0;
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
    public function barcodeUPCE(
        $height = 162,
        $lineWidth = 3,
        $txtPosition = '',
        $txtFont = '',
        $data = '123456789012'
    ) {
        $type = 1;
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
    public function barcodeCODABAR(
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
    public function barcodeMSI(
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
    public function barcodeCODE11(
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
            $this->connector->write(self::GS . "h" . chr($height));
            $this->connector->write(self::GS . 'w' . chr($lineWidth));
            $this->connector->write(self::GS . 'H' . chr($txtPosition));
            $this->connector->write(self::GS . 'f' . chr($txtFont));
            $this->connector->write(self::GS . "k" . chr($type) . $data . self::NUL);
        }
    }

    /**
     * 
     */
    public function barcodeQRCode()
    {
        
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
            $this->connector->write(self::LF);
        } else {
            $this->connector->write(self::ESC . "d" . chr($lines));
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
        $this->connector->write(self::ESC . "e" . chr($lines));
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
        $this->connector->write(self::GS . "V" . chr($mode) . chr($lines));
    }
    
    /**
     * Flush buffer data
     */
    public function send()
    {
        
    }
    
    /**
     * Close printer connetion
     */
    public function close()
    {
        $this->connector->close();
    }
}
