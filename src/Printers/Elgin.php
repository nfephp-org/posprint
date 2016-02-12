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
    
    
}
