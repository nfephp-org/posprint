<?php
include_once('../bootstrap.php');

use Posprint\Graphics\Graphics;

$graph = new Graphics();

$imgPath = '../images/tux.bmp';

$graph->load($imgPath);


$imgPathJPG = '../images/tux.jpg';
$graph->save($imgPathJPG, 'JPG', 100);