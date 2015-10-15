<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

//escolha o tipo do conector
use Posprint\Connectors\Serial;
//escolha a impressora
use Posprint\Printers\ZebraEpl;

//estabeleça os setups da conexão
$device = '/dev/ttyS1';
$baudRate = '9600';
$byteSize = '8';
$parity = 'none';
//inicie a conexão
$conn = new Serial($device, $baudRate, $byteSize, $parity);

//monte a mensagem
$msg = 'I8,A
q747
S2
O
JF
WN
ZT
Q464,25
N
A730,381,2,1,1,1,N,"Peca"
A730,205,2,1,1,1,N,"Produto"
A730,445,2,1,1,1,N,"Local"
A187,117,2,1,1,1,N,"Data Fabricacao"
A730,325,2,1,1,1,N,"Lote"
A730,85,2,1,1,1,N,"Peso Liquido"
A514,85,2,1,1,1,N,"Peso Bruto"
A730,261,2,1,1,1,N,"Referencia"
A730,149,2,1,1,1,N,"Cor"
A338,85,2,1,1,1,N,"Metragem"
A187,61,2,1,1,1,N,"Data Validade"
A730,21,2,2,1,1,N,"FIMATEC TEXTIL LTDA"
B466,447,2,3,2,5,102,N,"12345"
A356,339,2,3,1,1,N,"12345"
A730,427,2,4,1,1,N,"PH12"
A730,363,2,4,1,1,N,"12345"
A730,307,2,4,1,1,N,"222"
A187,99,2,4,1,1,N,"11/11/2011"
A730,243,2,4,1,1,N,"2324BCB00"
A730,187,2,4,1,1,N,"TECIDO TINTO"
A730,131,2,4,1,1,N,"BRANCO"
A730,67,2,4,1,1,N,"15,2KG"
A514,67,2,4,1,1,N,"16,1KG"
A338,67,2,4,1,1,N,"500MT"
A187,43,2,4,1,1,N,"11/11/2014"
P1
';
//como é um teste a mensagem foi montada de uma unica vez
//o certo seria invocar um método para cada linha de comando 
//a ser enviada para construir a etiqueta mas a classe ZebraEpl ainda
//não está completa

//instancie a impressora com a conexão estabelecida
$printer = new ZebraEpl($conn, true);
//use o metodo da impressora para enviar a mensagem para o buffer
$printer->text($msg);
//descarregue o buffer na conexão
$printer->send();
//feche a conexão
$printer->close();
