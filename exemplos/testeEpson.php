<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use Posprint\Printers\Epson;

$epson = new Epson();

$epson->initialize(); //inicializa a impressora
$epson->setCharset(2); //altera o charset para CP850

$epson->setBold(); //coloca em bold
$epson->text('Isso é um TESTE'); //imprime texto
$epson->setBold(); //detativa bold
$epson->text('Para imprimir essas linhas'); //imprime texto
$epson->line(); //insere linha reparadora

$resp = $epson->send('binS'); //obtem o conjunto de comandos para impressão em tela DEBUG
$fp = fopen('teste.prn', 'w+');
$fp = fwrite()
echo $resp; //apresenta comandos em tela

