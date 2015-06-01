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

use Posprint\Printers\Basic\Printer;
use Posprint\Printers\Basic\PrinterInterface;
use Posprint\Common\Graphics;

class Daruma extends Printer implements PrinterInterface
{
    public $charsetcode = 'ISO8859-1';
    
    protected $aCharSetTable = array(
        'ISO8859-1' => array('conv'=>'ISO8859-1','table'=>'0','desc'=>'ISO8859-1: Latin'),
        'CP850' => array('conv'=>'850','table'=>'1','desc'=>'PC850: Multilingual'),
        'ABICOMP' => array('conv'=>'','table'=>'2','desc'=>'ABICOMP'),
        'CP437' => array('conv'=>'437','table'=>'3','desc'=>'CP437')
    );

    
    /**
     * setPrinterMode
     * Seta o modo de impressão 
     * @param type $printerMode
     */
    public function setPrinterMode()
    {
        //[ESC] 198
        //  0
        //  XXXX
        //  n   zero cortado 0 = desligado, 1 - ligado
        //  n   Desabilita Teclado
        //  n   Guilhotina Habilitada
        //  n   Tipo do corte da guilhotina 0 = Total, 1 = Parcial
        //  X
        //  n   Numero de colunas 0 = 48 col, 1 = 52 col* 2 = 34 col**
        //  XXX
        //  n   Baudrate
        //  n   Controle de fluxo 0 = RTS/CTS, 1 = XON/XOFF
        //  XXXXXXXXXXXXXXXXX
        //  nn  00 a 20 = linhas de acionamento antes do corte da guilhotina
        //  n   1/2 = Tabela de comandos 1 ou 2
        //  n   Interchar delay (ms)
        //  XX
        //  n   CodePage
        $zerocortado = 0;
        $disablekeyboard = 0;
        $enablecutpartial = 0;
        $numcols = 0; //48colunas
        $baudrate = 8;
        //Baud Rate: 1 = 1200 2 = 2400 3 = 230400 (USB) 4 = 4800 5 = 57600
        //6 = 19200 7 = 38400 8 = 115200 (default para USB) 9 = 9600 (default para COM)
        $flowcontrol = 0; //0 = RTS/CTS, 1 = XON/XOFF
        $commandtable = 1; //1/2 = Tabela de comandos 1 ou 2
        $interchardelay = 0;
        $codepage = 0;
        $this->connector->write(
            self::ESC
            . chr(198)
            . chr(0)
            . 'XXXX'
            . chr($zerocortado)
            . chr($disablekeyboard)
            . chr($enablecutpartial)
            . 'X'
            . chr($numcols)
            . 'XXX'
            . chr($baudrate = 8)
            . chr($flowcontrol)
            . 'XXXXXXXXXXXXXXXXX'
            . chr($commandtable)
            . chr($interchardelay)
            . 'XX'
            . chr($codepage)
        );
    }
    
    /**
     * setCharset
     * Define a tabela de caracteres a ser usado pela impressora
     * Default ISO8859-1
     * @param string $charsetcode
     */
    public function setCharset($charsetcode = 'ISO8859-1')
    {   
        if (isset($this->aCharSetTable[$charsetcode])) {
            $this->charsetcode = $charsetcode;
        }    
    }
}
