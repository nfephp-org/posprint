<?php

namespace Posprint\Printers;

/**
 * Classe Daruma das impressoras POS.
 * 
 * Foi construída em torno dos comandos da DR700
 * Velocidade de Impressão: 150 mm/s (E), 200 mm/s (ETH) ou 300 mm/s (HE)
 * Resolução: 180 dpi
 * Papel:  57mm, 76mm, 80mm e 82,5 mm 
 * Largura de Impressão: Papel 80 mm (Max. 72 mm) (576 pontos) ou (Max. 78 mm) (624 pontos)
 * Colunas: Normal - 52 ou 48, Elite - 44 ou 40 ou Condensado - 62 ou 57
 * Largura de Impressão: Papel 58 mm (Máx. 54 mm)
 * 
 * NOTA: QR Code (firmware a partir de 2.50.00)
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Printers\DefaultPrinter;
use Posprint\Printers\Basic\PrinterInterface;


class Daruma extends DefaultPrinter
{
    public $aCountry = array('LATIN');
    
    public $aCodePage = array(    
        'ISO8859-1' => array('conv'=>'ISO8859-1','table'=>'0','desc'=>'ISO8859-1: Latin'),
        'CP850' => array('conv'=>'850','table'=>'1','desc'=>'PC850: Multilingual'),
        'ABICOMP' => array('conv'=>'','table'=>'2','desc'=>'ABICOMP'),
        'CP437' => array('conv'=>'437','table'=>'3','desc'=>'CP437')
    );

}
