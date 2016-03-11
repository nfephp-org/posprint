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
 * @copyright  Copyright (c) 2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Printers\DefaultPrinter;

final class Epson extends DefaultPrinter
{
    /**
     * Select printer mode
     * @param string $mode
     */
    public function setPrintMode($mode = null)
    {
        //not used for this printer
    }
}
