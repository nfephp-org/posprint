<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once('../bootstrap.php');

use Posprint\DanfcePos;
use Posprint\Printers\Daruma;
//se quizer usar impressora intalada localmente 
//use um conector apropriado, configure e passe para 
//classe de impressora desejada.

$printer = new Daruma();
$danfe = new DanfcePos($printer);
$filename = '../local/35160223050021000117650010000000091000000170-nfe.xml';

$danfe->loadNFCe($filename);
$danfe->monta();
$danfe->printDanfe();
