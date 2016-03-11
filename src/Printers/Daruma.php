<?php

namespace Posprint\Printers;

/**
 * Class Daruma printer POS.
 * 
 * Foi construída em torno dos comandos da DR700
 * Velocidade de Impressão: 150 mm/s (E), 200 mm/s (ETH) ou 300 mm/s (HE)
 * Resolução: 180 dpi
 * Papel:  57mm, 76mm, 80mm e 82,5 mm 
 * Largura de Impressão: Papel 80 mm (Max. 72 mm) (576 pontos) ou (Max. 78 mm) (624 pontos)
 * Colunas: Normal - 52 ou 48, Elite - 44 ou 40 ou Condensado - 62 ou 57
 * Largura de Impressão: Papel 58 mm (Máx. 54 mm)
 * 
 * NOTA: QR Code, apenas com firmware a partir de 2.50.00
 *       para firmwares anteriores é necessário criar uma imagem do QRCode
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Ristone Ribeiro Soares <ristone.soares at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Printers\DefaultPrinter;
use Posprint\Printers\PrinterInterface;

class Daruma extends DefaultPrinter implements PrinterInterface
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
     * Avaiable code pages for this printer
     * @var array
     */
    public $aCodePage = array(    
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
    protected $codepage = 'CP850';
    /**
     * Number of codpage in printer memory
     * @var int
     */
    protected $charsetTableNum = 1;
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
        $this->connector=$conn;
        $this->setCharset(1);
    }
}
