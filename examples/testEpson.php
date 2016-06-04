<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once('../bootstrap.php');

use Posprint\Printers\Epson;

$printer = new Epson();

$printer->text('Alô mundo. Cá estou eu !');

echo $printer->getBuffer();

die;