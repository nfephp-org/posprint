<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
include_once('../bootstrap.php');

use Posprint\Printers\Daruma;

$printer = new Daruma();
try {
    $printer->barcode('12345678','I25', 50, 2);
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage();
    die;
}

$printer->text('Alô mundo. Cá estou eu !');

echo $printer->getBuffer();