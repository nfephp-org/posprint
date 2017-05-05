<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once('../bootstrap.php');

use Posprint\Graphics\Graphics;

try {
    $gr = new Graphics();
    $text = 'http://www.dfeportal.fazenda.pr.gov.br/dfe-portal/rest/servico/consultaNFCe?chNFe=41170410422724000187650010000000071000012588&nVersao=100&tpAmb=1&dhEmi=323031372d30342d32375431333a32353a34342d30333a3030&vNF=9.90&vICMS=0.00&digVal=642b4738654b6d58334e49477a49686264475335434c4d697347343d&cIdToken=000001&cHashQRCode=58AD28AB7ABF1E610486F4077B1D6064BF64BB39';
    $gr->imageQRCode($text, 200, 10, $gr::MEDIUM);

    header('Content-Type: '.'image/png');
    imagepng($gr->img);
} catch (\Exception $e) {
    echo $e->getMessage();
}    

