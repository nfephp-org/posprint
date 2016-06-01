<?php
include_once('../bootstrap.php');

use Posprint\Graphics\Graphics;

$graph = new Graphics();

$imgPath = 'tux.png';

$graph->load($imgPath);


$imgPathBW = 'tux.bmp';
$graph->save($imgPathBW, 'BMP');