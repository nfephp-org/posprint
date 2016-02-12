<?php

namespace Posprint\Printers\Basic;

/**
 * Classe Basic das impressoras termicas.
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Connectors\Buffer;

abstract class Basic
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
    
    //public property standards
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
     *
     * @var type 
     */
    protected $connector;
    /**
     * Selected Charset Code
     * @var int
     */
    protected $charsetcode = 0;
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
     * Seleted code page
     * Defined in printer class
     * @var string
     */
    protected $codepage = 'WINDOWS-1250';
    /**
     * Selected Country page
     * Defined in printer class
     * @var type 
     */
    protected $country = 'LATIN';
    /**
     * Selected bold mode
     * @var bool
     */
    protected $boldMode = false;
    /**
     * Selected italic mode
     * @var bool
     */
    protected $italicMode = false;
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
        $this->buffer = new Connectors\Buffer();
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
     * @return string|array
     */
    public function getCodePages($all = false)
    {
        $keys = array_keys($this->aCodePage);
        if ($all) {
            return $keys;
        }
        return $this->codepage;
    }
    
    /**
     * Send message or command to buffer
     * @param string $text
     */
    public function text($text = '')
    {
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
                $resp = $this->buffer->getDataReadable(true);
        }
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

    //abstract methods
    abstract public function setPaperWidth($width = 80);
    abstract public function setMargins($left = 0, $right = 0);
    abstract public function setSpacing($horizontal = 30, $vertical = 30);
    abstract public function setCharSpacing($value = 3);
    abstract public function setParagraf($paragrafo = 0);
    abstract public function setPrintMode();
    abstract public function setFont($font = 'A');
    abstract public function setCharset();
    abstract public function setInternational();
    abstract public function setBold();
    abstract public function setItalic();
    abstract public function setUnderlined();
    abstract public function setExpanded();
    abstract public function setCondensed();
    abstract public function setRotate90();
    abstract public function setReverseColors();
    abstract public function setJustification();
    abstract public function initialize();
    abstract public function feed();
    abstract public function feedReverse();
    abstract public function pulse();
    abstract public function cut();
}
