<?php

namespace Posprint\Printers;

/**
 * Classe Bematech das impressoras POS.
 * 
 * Foi construída em torno dos comandos da Bematech MP-4200 TH
 * Velocidade de impressão 250 mm/s
 * Resolução: 203 dpi
 * Largura de Impressão: Papel 80 mm (Máx. 72 mm) / Papel 58 mm (Máx. 54 mm)
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


class Bematech extends Printer implements PrinterInterface
{
    public $mode = 'ESCBEMA';
    
    public $charsetcode = 'ISO8859-1';
    
    protected $aCharSetTable = array(
        'ISO8859-1' => array('conv'=>'ISO8859-1','table'=>'0','desc'=>'ISO8859-1: Latin'),
        'CP850' => array('conv'=>'850','table'=>'1','desc'=>'PC850: Multilingual'),
        'ABICOMP' => array('conv'=>'','table'=>'2','desc'=>'ABICOMP'),
        'CP437' => array('conv'=>'437','table'=>'3','desc'=>'CP437')
    );
    
    /**
     * setPrinterMode
     * Seta o modo de impressão no caso da Bematech seleciona entre o 
     * padão de comandos ESC/BEMA e ESC/POS alternativo
     * @param type $printerMode
     */
    public function setPrinterMode($printerMode = 'ESCBEMA')
    {
        //padrão é ESC/BEMA
        $nmode = 0;
        if ($printerMode != 'ESCBEMA') {
            $this->printerMode = 'ESCPOS';
            $nmode = 1;
        }
        $this->connector->write(self::GS . chr(249) . chr(53) . $nmode);
    }
    
    /**
     * initialize
     * Inicializa a impressora
     * All printer settings, including character font, line spacing,
     * left margin, right margin and inverted mode are canceled and 
     * the printer returns to its initial state.
     * 
     * @param string $mode 
     */
    public function initialize($mode = 'ESCBEMA')
    {
        $this->connector->write(self::ESC . "@");
        $this->characterTable = 0;
        if ($mode != 'ESCBEMA') {
            $this->setPrinterMode($mode);
        }
    }
    
    
    
}
