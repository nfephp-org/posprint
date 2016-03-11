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

use Posprint\Printers\DefaultPrinter;

final class Bematech extends DefaultPrinter
{
    public $mode = 'ESCPOS';
    
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
     * padrão de comandos ESC/POS e alternativo ESC/BEMA
     * @param type $printerMode
     */
    public function setPrinterMode($printerMode = 'ESCPOS')
    {
        //padrão é ESC/POS
        $nmode = 0;
        if ($printerMode == 'ESCBEMA') {
            $this->printerMode = 'ESCBEMA';
            $nmode = 1; //???
        }
        //$this->buffer->write(self::GS . chr(249) . chr(53) . $nmode);
    }
    
    /**
     * 
     * @param string $data Message to be printe in QRCode
     * @param string $level Corretion level 
     * @param string $model model of QRCode
     * @param int $wmod width of module/cell size in pixels
     * @param bool $micro if true sets to micro-qrcode, normal otherelse
     */
    public function barcodeQRCode($data = '', $level = 'L', $model = '1', $wmod = 4, $micro = false)
    {
        /**
         * GS k Q n1 n2 n3 n4 n5 n6 d1 ... dn
         *
         * 0 ≤ n1 ≤ 3
         * 0 ≤ n2 ≤ 255
         * 0 ≤ n3 ≤ 39
         * 0 ≤ n4 ≤ 3
         * 
         * [n1]   Error correction level (data restoration)
         *        0    Level L Approx. 7% of codewords can be restored.
         *        1    Level M Approx. 15% of codewords can be restored. 
         *        2    Level Q Approx. 25% of codewords can be restored.
         *        3    Level H Approx. 30% of codewords can be restored.
         * 
         * [n2]   MSB 7 6 5 4 3 2 1                         0 LSB 
         *        Module/cell size in pixels                0 – QRCode and
         *        1 ≤ module size ≤ 127                     1 – Micro QRCode
         *        0 – The module size is 4 for default
         *
         * [n3]   Version QRCode
         *        The symbol versions of QR Code range from Version 1 to Version
         *        40. Each version has a different module configuration or number
         *        of modules. (The module refers to the black and white dots that
         *        make up QR Code.)
         *        "Module configuration" refers to the number of modules contained
         *        in a symbol, commencing with Version 1 (21 × 21 modules) up to
         *        Version 40 (177 × 177 modules). Each higher version number
         *        comprises 4 additional modules per side.
         *        Each QR Code symbol version has the maximum data capacity
         *        according to the amount of data, character type and error
         *        correction level
         * 
         * [n4]   Encoding modes        QR Code Data capacity
         *               0              Numeric only Max. 7,089 characters
         *               1              Alphanumeric Max. 4,296 characters
         *               2              Binary (8 bits) Max. 2,953 bytes
         *               3              Kanji, full-width Kana Max. 1,817 characters
         * 
         * 
         */
        //Corretion Level
        switch ($level) {
            case 'L':
                $n1 = 0;
                $ec = 1;
                break;
            case 'M':
                $n1 = 1;
                $ec = 0;
                break;
            case 'Q':
                $n1 = 2;
                $ec = 3;
                break;
            case 'H':
                $n1 = 3;
                $ec = 2;
                break;
            default:
                $n1 = 0;
                $ec = 1;
        }
        //module size and QRCode type (micro or normal)
        if (!($wmod >= 1 && $wmod <= 127)) {
            $wmod = 4; //use default
        }
        $n2 = 0;
        if ($micro) {
            $n2 = 1;
        }
        $n2 = ($n2 + $wmod *2);
        //o numero de versão do QRCode depende o comprimento dos dados a serem impressos
        // e do nivel de correção, considerando a impressão somente como alfanumerica
         
        
        
    }
    
    private static function zCalcQRVersion($data, $ec)
    {
        // considerando apenas alfanumericos
        $codeword_num_plus = array(0,0,0,0,0,0,0,0,0,0,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,4,4,4,4,4,4,4,4,4,4,4,4,4,4, );
        $max_data_bits_array = array(
        0,128,224,352,512,688,864,992,1232,1456,1728,
        2032,2320,2672,2920,3320,3624,4056,4504,5016,5352,
        5712,6256,6880,7312,8000,8496,9024,9544,10136,10984,
        11640,12328,13048,13800,14496,15312,15936,16816,17728,18672,
        152,272,440,640,864,1088,1248,1552,1856,2192,
        2592,2960,3424,3688,4184,4712,5176,5768,6360,6888,
        7456,8048,8752,9392,10208,10960,11744,12248,13048,13880,
        14744,15640,16568,17528,18448,19472,20528,21616,22496,23648,
        72,128,208,288,368,480,528,688,800,976,
        1120,1264,1440,1576,1784,2024,2264,2504,2728,3080,
        3248,3536,3712,4112,4304,4768,5024,5288,5608,5960,
        6344,6760,7208,7688,7888,8432,8768,9136,9776,10208,
        104,176,272,384,496,608,704,880,1056,1232,
        1440,1648,1952,2088,2360,2600,2936,3176,3560,3880,
        4096,4544,4912,5312,5744,6032,6464,6968,7288,7880,
        8264,8920,9368,9848,10288,10832,11408,12016,12656,13328,
        );
        $len = strlen($data);
        $i = 1 + 40 * $ec;
        $j = $i + 39;
        $qrversion = 1;
        while ($i <= $j) {
            if (($max_data_bits_array[$i]) >= $len + $codeword_num_plus[$qrversion]) {
                break;
            }
            $i++;
            $qrversion++;
        }
        return $qrversion;
    }
}
