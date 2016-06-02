<?php
include_once('../bootstrap.php');

use Posprint\Graphics\Graphics;

$graph = new Graphics();

$imgPath = '../images/tux.png';

$graph->load($imgPath);


$imgPathBW = '../images/tux.bmp';
$graph->save($imgPathBW, 'BMP');