<?php

namespace Posprint\Printers;

use Posprint\Connectors;

abstract class Basic
{
     //constantes padrões
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
    
    //propriedades publicas padrões
    public $dpi = 203;
    public $dpmm = 8;
    public $widthMaxmm = 80;//mm
    public $widthPaper = 80;//mm
    public $widthPrint = 72;//mm
    public $widthMaxdots = 576;//dots
    public $maxchars = 48;//max characters per line
    public $buffer = '';
    
    //propriedades protegidas padrões
    protected $connector;
    protected $charsetcode = 0;
    protected $font = 'A';
    protected $printerMode = 'normal';
    protected $codepage = 'WINDOWS-1250';
    protected $country = 'LATIN';
    protected $bufferize = false;
    
    public function __construct($connector = null, $bufferize = true)
    {
        $this->bufferize = $bufferize;
        $this->buffer = new Connectors\Buffer();
        if (isNull($connector)) {
            $this->connector = new Connectors\Buffer();
        }
    }
    
    //metodos padrões
    public function setDevice()
    {
        
    }
    
    public function text($text = '')
    {
        if ($this->bufferize) {
            $this->buffer->write($text);
        } else {
            $this->connector->write($text);
        }
    }
    
    public function line()
    {
        $text = str_repeat('-', $this->maxchars);
        $this->text($text);
    }
    
    public function close()
    {
        $this->connector->close();
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
    abstract public function send();
}
