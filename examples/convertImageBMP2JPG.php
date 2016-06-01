<?php
include_once('../bootstrap.php');

use Posprint\Graphics\Graphics;

$graph = new Graphics();

$imgPath = 'tux.bmp';

$graph->load($imgPath);


$imgPathJPG = 'tux.jpg';
$graph->save($imgPathJPG, 'JPG', 100);