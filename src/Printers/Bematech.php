<?php

namespace Posprint\Printers;

/**
 * Bematech class for POS printer
 * Model: MP 4200TH
 *
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Printers\DefaultPrinter;
use Posprint\Printers\PrinterInterface;

final class Bematech extends DefaultPrinter implements PrinterInterface
{
    /**
     * Select printer mode
     *
     * @param string $mode
     */
    public function setPrintMode($mode = 'ESCPOS')
    {
        //padrão é ESC/POS
        $nmode = 0;
        if ($mode == 'ESCBEMA') {
            $this->printerMode = 'ESCBEMA';
            $nmode = 1;
        }
        $this->buffer->write(self::GS . chr(249) . chr(32) . $nmode);
    }

    /**
     * Imprime o QR Code
     *
     * @param string $data   Dados a serem inseridos no QRCode
     * @param string $level  Nivel de correção L,M,Q ou H
     * @param int    $modelo modelo de QRCode 
     * @param int    $wmod   largura da barra 3 ~ 16
     */    
    public function barcodeQRCode($data = '', $level = 'M', $modelo = 2, $wmod = 4)
    {
        //essa matriz especifica o numero máximo de caracteres alfanumericos que o 
        //modelo de QRCode suporta dependendo no nivel de correção.
        //Cada matriz representa um nivel de correção e cada uma das 40 posições nessas 
        //matrizes indicam o numero do modelo do QRCode e o numero máximo de caracteres
        //alfamunéricos suportados
        //Quanto maior o nivel de correção menor é a quantidade de caracteres suportada
        $aModels[0]=[25,47,77,114,154,195,224,279,335,395,468,535,619,667,758,854,938,1046,1153,1249,1352,1460,1588,1704,1853,1990,2132,2223,2369,2520,2677,2840,3009,3183,3351,3537,3729,3927,4087,4296];
        $aModels[1]=[20,38,61,90,122,154,178,221,262,311,366,419,483,528,600,656,734,816,909,970,1035,1134,1248,1326,1451,1542,1637,1732,1839,1994,2113,2238,2369,2506,2632,2780,2894,3054,3220,3391];
        $aModels[2]=[16,29,47,67,87,108,125,157,189,221,259,296,352,376,426,470,531,574,644,702,742,823,890,963,1041,1094,1172,1263,1322,1429,1499,1618,1700,1787,1867,1966,2071,2181,2298,2420];
        $aModels[3]=[10,20,35,50,64,84,93,122,143,174,200,227,259,283,321,365,408,452,493,557,587,640,672,744,779,864,910,958,1016,1080,1150,1226,1307,1394,1431,1530,1591,1658,1774,1852];
        //n1 Error correction level (data restoration)
        switch ($level) {
            case 'L':
                $n1 = 0;
                break;
            case "M":
                $n1 = 1;
                break;
            case "Q":
                $n1 = 2;
                break;
            case "H":
                $n1 = 3;
                break;
            default:
                $n1 = 0;
        }
        //n2 Module/cell size in pixels MSB 1 ≤ module size ≤ 127 LSB 0 QR or 1 MicroQR
        $n2 = $wmod << 2;
        //comprimento da mensagem
        $length = strlen($data);
        //seleciona matriz de modelos aplicavel pelo nivel de correção
        $am = $aModels[$n1];
        $i = 0;
        $flag = false;
        foreach($am as $size) {
            //verifica se o tamanho maximo é maior ou igual ao comprimento da mensagem
            if ($size >= $length) {
                $flag = true;
                break;
            }
            $i++;
        }
        if (! $flag) {
            throw new InvalidArgumentException('O numero de caracteres da mensagem é maior que a capacidade do QRCode');
        }
        //n3 Version QRCode
        //depende do comprimento dos dados e do nivel de correção
        $n3 = ($i + 1); 
        //n4 Encoding modes
        //0 – Numeric only              Max. 7,089 characters
        //1 – Alphanumeric              Max. 4,296 characters
        //2 – Binary (8 bits)           Max. 2,953 bytes
        //3 – Kanji, full-width Kana    Max. 1,817 characters
        $n4 = 1;//sempre será 1 apenas caracteres alfanumericos nesse caso
        //n5 e n6 Indicate the number of bytes that will be coded, where total = n5 + n6 x 256, and total must be less than 7089.
        $n6 = intval($length / 256);
        $n5 = ($length % 256);
        $this->buffer->write(self::GS."kQ" . chr($n1) . chr($n2) . chr($n3) . chr($n4) . chr($n5) . chr($n6) . $data);
    }
}
