<?php

namespace Posprint\Printers;

/**
 * Classe Sweda das impressoras POS.
 * 
 * Foi construída em torno dos comandos do modelo SI 300S e SI 300L
 * Velocidade de Impressão: Máx. 220mm/s
 * Resolução: 180 dpi
 * Largura de Impressão: Papel 80 mm (Máx. 72 mm)
 * Largura de Impressão: Papel 58 mm (Máx. 54 mm)
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
use Posprint\Common\Graphics;

class Sweda extends Printer implements PrinterInterface
{
    
    public function _construct()
    {
        $this->dpi = 180;
        $this->dpmm = 7;
    }
    
}
