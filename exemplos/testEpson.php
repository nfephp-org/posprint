<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use Posprint\Printers\Epson;

$printer = new Epson();