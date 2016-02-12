<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

/*
use Posprint\Graphics\GdtoBMP;
use Posprint\Graphics\Grf;
use Posprint\Graphics\DitheredImageProvider;


echo DitheredImageProvider::convertByteToGrayscale(231);
*/
/*
$image = file_get_contents('logo.jpg');
$img = imagecreatefromstring($image);

$owidth = imagesx($img);
$oheight = imagesy($img);

$n = ceil($owidth/8);
if ($owidth%8 != 0) {
    $width = ($n+1)*8;
}

$height = (($oheight * $width) / $owidth);

$newimg = imagecreatetruecolor($width, $height);
imagecopyresized($newimg, $img, 0, 0, 0, 0, $width, $height, $owidth, $oheight);

imagefilter($newimg, IMG_FILTER_GRAYSCALE);
imagefilter($newimg, IMG_FILTER_CONTRAST, -1000);

$bmp = GdtoBMP::convert($newimg);
file_put_contents('teste.bmp', $bmp);

$resp = Grf::convert('teste.bmp');

echo "Fim";
*/
/*
if(extension_loaded('gd')) {
    print_r(gd_info());
} else {
    echo 'GD is not available.';
}

if(extension_loaded('imagick')) {
    $imagick = new Imagick();
    print_r($imagick->queryFormats());
} else {
    echo 'ImageMagick is not available.';
}
*/
/*
$image = file_get_contents('logo.jpg');
$img = imagecreatefromstring($image);
imagefilter($img, IMG_FILTER_GRAYSCALE);
imagefilter($img, IMG_FILTER_CONTRAST, -1000);
$width = imagesx($img);
$height = imagesy($img);
//imagepng($img, 'logoBW.png');
imagedestroy($img);

echo "Largura: $width <br>"; 
echo "Altura: $height <br>";
*/