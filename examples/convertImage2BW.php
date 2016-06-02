<?php
include_once('../bootstrap.php');

use Posprint\Graphics\Graphics;

$graph = new Graphics();

$imgPath = '../images/tux.png';

$graph->load($imgPath);

$graph->convertBW();

$imgPathBW = '../images/tuxBW.png';
$graph->save($imgPathBW, 'PNG');