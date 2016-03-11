<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

//escolha o tipo do conector
use Posprint\Connectors\Serial;
//escolha a impressora
use Posprint\Printers\Daruma;

//estabeleça os setups da conexão
/*
$device = 'COM1';
$baudRate = '9600';
$byteSize = 8;
$parity = 'none';
//inicie a conexão
$conn = new Serial($device, $baudRate, $byteSize, $parity);
*/
$conn = new Posprint\Connectors\Buffer();

//instancie a impressora com a conexão estabelecida
$printer = new Daruma($conn);

$printer->initialize();

$printer->setPrintMode();

$msg = '------------------ FONTE NORMAL ----------------'
        . ' !"#$%\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNO'
        . 'PQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvxz{|}~';
//Envia o texto para o buffer
$printer->text($msg);

//Altera a fonte para italico
$printer->setItalic(1);

//Altera a fonte para expandido
$printer->setExpanded(0,1);


$printer->setParagraph(0);
$msg = 'ITÁLICO';
//Envia o texto formatado para o buffer
$printer->text($msg);

//Desativa a fonte expandida
$printer->setExpanded();
$printer->setParagraph(1);
$msg = '0123456789ABCDEFGHIJKLMNOPabcdefghijklmnop{}!@#(';

$printer->text($msg);

//Altera a fonte para Bold
$printer->setBold();
$printer->setParagraph(1);
$msg = '0123456789ABCDEFGHIJKLMNOPabcdefghijklmnop{}!@#(';
$printer->text($msg);

//Como o Bold já foi setado agora é necessario enviar novamente para desativa-lo
$printer->setBold();
$printer->setItalic();

$printer->setExpanded(0,1);
$printer->setParagraph(0);
$msg = "EXPANDIDO";
$printer->text($msg);

$printer->setParagraph(1);
$msg = "0123456789ABCDEFGHIJabcd";
$printer->text($msg);

$printer->setBold();

$msg = "0123456789ABCDEFGHIJabcd";
$printer->text($msg);

//Desliga o bold e expandido
$printer->setBold();
$printer->setExpanded();

$printer->setCondensed();
$printer->setParagraph(0);
$msg = 'CONDENSADO';

$printer->text($msg);

$printer->setParagraph(1);
$msg = '0123456789ABCDEFGHIJKLMNOPQRSTUVXYZabcdefghijklmnopqeruv';
$printer->text($msg);

//liga o bold novamente
$printer->setBold();
$printer->setParagraph(1);
$msg = '0123456789ABCDEFGHIJKLMNOPQRSTUVXYZabcdefghijklmnopqeruv';
$printer->text($msg);
//Desliga novamente
$printer->setBold();

//Seta dupla altura
$printer->setExpanded(1);
$printer->setParagraph(0);
$msg = 'DUPLA ALTURA';
$printer->text($msg);

$printer->setParagraph(1);
$msg = '012345678';
$printer->text($msg);

//Desativa dupla altura
$printer->setExpanded();
$msg = 'ABCD';
$printer->text($msg);

//Ativa novamente dupla altura
$printer->setExpanded(1);
$msg = 'ABCD';
$printer->text($msg);

$printer->initialize();

$printer->setParagraph();

$msg = 'EAN-13';
$printer->setParagraph(1);
$printer->text($msg);
$printer->barcodeEAN13('b');
$printer->setParagraph();

$msg = 'EAN-8(Vertical)';
$printer->setParagraph(1);
$printer->text($msg);
$printer->barcodeEAN8('a');

$msg = 'CODE-39';
$printer->setParagraph(1);
$printer->text($msg);
$printer->barcode39('b');
$printer->setParagraph(1);

$msg = 'CODE-93';
$printer->setParagraph(1);
$printer->text($msg);
$printer->barcode93('b');
$printer->setParagraph(1);


$msg = 'CODE-128';
$printer->setParagraph(1);
$printer->text($msg);
$printer->barcode128('b');
$printer->setParagraph(1);


//Impressão do barcodeQRCode($data, $width, $redundancy)
$msg = 'Qr-code';
$printer->setParagraph(1);
$printer->text($msg);
$printer->barcodeQRCode();

//Adianta 4 linhas
$printer->feed(15);

$printer->cut();

$printer->pulse();
//descarregue o buffer na conexão
$printer->send();

//feche a conexão
$printer->close();
