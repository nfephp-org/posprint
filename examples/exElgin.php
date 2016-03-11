<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use Posprint\Printers\Elgin;

$printer = new Elgin();

$printer->initialize(); //inicializa a impressora
$printer->text('Isso é um TESTE'); //imprime texto
$printer->text('Para imprimir essas linhas'); //imprime texto


$resp = $printer->send('binS'); //obtem o conjunto de comandos para impressão em tela DEBUG
//$resp = str_replace("\n", "<br>", $resp);
file_put_contents('elgin_teste.txt', $resp);

//echo $resp; //apresenta comandos em tela


