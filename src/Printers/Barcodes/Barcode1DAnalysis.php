<?php

namespace Posprint\Printers\Barcodes;

class Barcode1DAnalysis
{
    protected static $barcode1D = [
        'EAN13' => ['len' => '12', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9]/'],
        'EAN8' => ['len' => '7',  'oddeven' => '', 'code' => '', 'regex' => '/[^0-9]/'],
        'S25' => ['len' => '1-255', 'oddeven' => 'even', 'code' => '', 'regex' => '/[^0-9]/'],
        'I25' => ['len' => '1-255', 'oddeven' => 'even', 'code' => '', 'regex' => '/[^0-9]/'],
        'CODE128' => ['len' => '2-255', 'oddeven' => '', 'code' => 'ASCII', 'regex' => ''],
        'CODE39' => ['len' => '1-255', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9A-Z-,.%\/$ +]/'],
        'CODE93' => ['len' => '1-255', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9A-Z-,.%\/$ +]/'],
        'UPC_A' => ['len' => '11', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9]/'],
        'UPC-E' => ['len' => '12', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9]/'],
        'CODABAR' => ['len' => '1-255', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9$-:\/.+/'],
        'MSI' => ['len' => '1-20', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9]/'],
        'CODE11' => ['len' => '1-20', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9]/'],
        'GS1128' => ['len' => '2-255', 'oddeven' => '', 'code' => 'ASCII', 'regex' => ''],
        'GS1DATABAROMINI' => ['len' => '13', 'code' => '', 'oddeven' => '', 'regex' => '/[^0-9]/'],
        'GS1DATABARTRUNC' => ['len' => '13', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9]/'],
        'GS1DATABARLIMIT' => ['len' => '13', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9]/'],
        'GS1DATABAREXPAN' => ['len' => '2-41', 'oddeven' => '', 'code' => '', 'regex' => '/[^0-9A-Za-z]/']
    ];
    
    /**
     * Adjust data to barcode parameters
     *
     * @param string $data barcode data
     * @param string $type type of barcode
     * @return string|boolean
     */
    public static function validate($data, $type)
    {
        if (!array_key_exists($type, self::$barcode1D)) {
            return false;
        }
        $bcSpks = self::$barcode1D[$type];
        $len = $bcSpks['len'];
        $oddeven = $bcSpks['oddeven'];
        $regex = $bcSpks['regex'];
        $code = $bcSpks['code'];
        //apply rules over barcode data
        if ($code == 'ASCII') {
            $data = iconv('utf-8', 'us-ascii//TRANSLIT', $data);
        }
        if ($regex != '') {
            $data = preg_replace($regex, '', $data);
        }
        $data = trim($data);
        if (empty($data)) {
            return false;
        }
        $dlen = strlen($data);
        if (($oddeven == 'even' && $dlen % 2 != 0)
            || ($oddeven == 'odd' && $dlen % 2 == 0)
        ) {
                return false;
        }
        $al = explode('-', $len);
        if (count($al) > 1) {
            if ($dlen < $al[0]) {
                return false;
            }
            $alen = $al[1];
        } else {
            $alen = $al[0];
        }
        $data = substr($data, 0, $alen);
        return $data;
    }
}
