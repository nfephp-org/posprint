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

use Posprint\Printers\DefaultPrinter;
use Posprint\Printers\PrinterInterface;

class Elgin extends DefaultPrinter implements PrinterInterface
{
    /**
     * Connector to printer
     * @var ConnectorInterface
     */
    private $connector;
    /**
     * Avaiable country page for this printer
     * @var array 
     */
    public $aCountry = array('LATIN');
    /**
     * List all available code pages
     * @var array
     */
    protected $aCharSetTable = array(
        'ISO8859-1' => array('conv'=>'ISO8859-1','table'=>'0','desc'=>'ISO8859-1: Latin'),
        'CP850' => array('conv'=>'850','table'=>'1','desc'=>'PC850: Multilingual'),
        'ABICOMP' => array('conv'=>'','table'=>'2','desc'=>'ABICOMP'),
        'CP437' => array('conv'=>'437','table'=>'3','desc'=>'CP437')
    );
    
    /**
     * Seleted code page
     * Defined in printer class
     * @var string
     */
    protected $codepage = 'ISO8859-1';
    /**
     * Number of codpage in printer memory
     * @var int
     */
    protected $charsetTableNum = 0;
    /**
     * Selected Country page
     * Defined in printer class
     * @var type 
     */
    protected $country = 'LATIN';
    
    /**
     * Construct class and set the initial code page
     * @param ConnectorInterface $conn
     */
    public function __construct(ConnectorInterface $conn = null){
        parent::__construct();
        if (is_null($conn)) {
            $this->connector = new Posprint\Connectors\Buffer();
        }    
        $this->setCharset(0);
    }
    
    /**
     * Prints QR barcode
     * @param string $texto
     * @param string $level
     * @param string $modelo
     * @param int $wmod
     */
    public function barcodeQRCode($texto = '', $level = 'L', $modelo = '1', $wmod = 1)
    {
        $cn = '1'; // Code type for QR code
        // Select model: 1, 2 or micro.
        $this->writeDataQRCode(chr(65), $cn, chr(48 + $modelo) . chr(0));
        // Set dot size.
        $this->writeDataQRCode(chr(67), $cn, chr($wmod));
        // Set error correction level: L, M, Q, or H
        $this->writeDataQRCode(chr(69), $cn, chr(48 + $level));
        // Send content & print
        $this->writeDataQRCode(chr(80), $cn, $texto, '0');
        $this->writeDataQRCode(chr(81), $cn, '', '0');
    }
    
    /**
     * Writes command to buffer and caculate the header of command
     * @param string $fn
     * @param string $cn
     * @param string $data
     * @param string $m
     */
    private function writeDataQRCode($fn, $cn, $data = '', $m = '')
    {
        $header = $this->intLowHigh(strlen($data) + strlen($m) + 2, 2);
        $this->buffer->write(self::GS . "(k" . $header . $cn . $fn . $m . $data);
    }
}
