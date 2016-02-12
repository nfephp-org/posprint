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

use Posprint\Connectors;

abstract class Basic
{
     //constantes padrões
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
    
    //propriedades publicas padrões
    public $dpi = 203; //dots per inch
    public $dpmm = 8; //dots per mm
    public $widthMaxmm = 80;//mm
    public $widthPaper = 80;//mm
    public $widthPrint = 72;//mm
    public $widthMaxdots = 576;//dots
    public $maxchars = 48;//max characters per line
    public $buffer = array();
    
    //propriedades protegidas padrões
    protected $connector;
    protected $charsetcode = 0;
    protected $font = 'A';
    protected $printerMode = 'normal';
    protected $codepage = 'WINDOWS-1250';
    protected $country = 'LATIN';
    protected $bufferize = false;
    
    public function __construct($connector = null, $bufferize = false)
    {
        $this->bufferize = $bufferize;
        $this->buffer = new Connectors\Buffer();
        if ($connector === null) {
            $this->connector = new Connectors\Buffer();
        } else {
            $this->connector = $connector;
        }
    }
    
    /**
     * 
     * @param bool $all
     * @return mixed
     */
    public function getCountries($all = true)
    {
        if ($all) {
            return $this->aCountry;
        }
        return $this->country;
    }
    
    /**
     * 
     * @param bool $all
     * @return mixed
     */
    public function getCodePages($all = true)
    {
        $keys = array_keys($this->aCodePage);
        if ($all) {
            return $keys;
        }
        return $this->codepage;
    }
    
    
    public function text($text = '')
    {
        $this->zWriteToConn($text);
    }

    public function line()
    {
        $text = str_repeat('-', $this->maxchars);
        $this->text($text);
    }
    
    public function close()
    {
        $this->connector->close();
        $this->buffer->close();
    }
    
    public function send($all = true)
    {
        $text = $this->buffer->getDataBinary(false);
        $this->connector->write($text);
    }

    protected function zWriteToConn($text = '')
    {
        if ($this->bufferize) {
            $this->buffer->write($text);
        } else {
            $this->connector->write($text);
        }
    }
    
    protected function getWordLength($texto = '')
    {
        //k = (pL + pH × 256) – 3
        $len = strlen($texto);
    }


    //métodos abstratos
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
