<?php

namespace Posprint\Printers;

/**
 * Classe Elgin das impressoras POS.
 * 
 * Foi construída em torno dos comandos do modelo i9
 * Velocidade de Impressão: Até 300 mm/s
 * Resolução: 203 DPI (8 dots/mm)
 * Largura: 57,5 ± 0,5mm (54 mm)
 * Largura: 80 ± 0,5mm (72mm)
 * 
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

class Elgin extends Printer implements PrinterInterface
{
    public $charsetcode = 'ISO8859-1';
    
    protected $aCharSetTable = array(
        'ISO8859-1' => array('conv'=>'ISO8859-1','table'=>'0','desc'=>'ISO8859-1: Latin'),
        'CP850' => array('conv'=>'850','table'=>'1','desc'=>'PC850: Multilingual'),
        'ABICOMP' => array('conv'=>'','table'=>'2','desc'=>'ABICOMP'),
        'CP437' => array('conv'=>'437','table'=>'3','desc'=>'CP437')
    );
    
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
        $this->text(self::ESC . "@", self::NOTRANS);
    }
    
    public function setPaperWidth($width = 80)
    {
        
    }
    public function setMargins($left = 0, $right = 0)
    {
        
    }
    public function setSpacing($horizontal = 30, $vertical = 30)
    {
        
    }
    public function setCharSpacing($value = 3)
    {
        
    }
    public function setParagraph($value = 0)
    {
        
    }
    public function setPrintMode()
    {
        
    }
    public function setFont($font = 'A')
    {
        
    }
    public function setCharset()
    {
        
    }
    public function setInternational()
    {
        
    }
    public function setBold()
    {
        
    }
    public function setItalic()
    {
        
    }
    public function setUnderlined()
    {
        
    }
    public function setExpanded()
    {
        
    }
    public function setCondensed()
    {
        
    }
    public function setRotate90()
    {
        
    }
    public function setReverseColors()
    {
        
    }
    public function setJustification($value = 'L')
    {
        
    }
    public function feed()
    {
        
    }
    public function feedReverse()
    {
        
    }
    public function pulse()
    {
        
    }
    public function cut()    
    {
        
    }
    public function barcodeEAN13()
    {
        
    }
    public function barcodeEAN8()
    {
        
    }
    public function barcode25()
    {
        
    }
            
    public function barcode39()
    {
        
    }
    public function barcode93()
    {
        
    }
    public function barcode128()
    {
        
    }
    public function barcodeQRCode()
    {
        
    }
    public function barcodePdf417()
    {
        
    }
    public function putImage()
    {
        
    }
    
    
}
