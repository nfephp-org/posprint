<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once('../bootstrap.php');

use Posprint\Printers\Daruma;
use Posprint\Connectors\Usb;

try {
    $connector = null;
    //$connector = new Usb('/dev/ttyACM0');
    $printer = new Daruma($connector);
    $printer->barcode('12345678','I25', 50, 2);
    $printer->lineFeed(2);
    $printer->text('Alô mundo. Cá estou eu !');
    $printer->lineFeed(1);
    $printer->putImage('../images/tux.png');
    $printer->lineFeed(1);
    $printer->setBold();
    $printer->text('Voltei agora em negrito !');
    $printer->setBold();
    $printer->lineFeed(4);
    $printer->barcodeQRCode('Este é um teste de QRCode', 'M', 2, 4);
    //recupera o buffer para leitura humana 
    echo $printer->getBuffer();
    //envia para a impressora se houver uma conexão
    $printer->send();
    
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage();
    die;
} catch (\RuntimeException $e) {
    echo $e->getMessage();
    die;
}

echo "FIM";