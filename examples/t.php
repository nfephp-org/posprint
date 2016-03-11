<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

$bmp = 'logo.bmp';

$grf = Posprint\Common\Grf::bmp2grf($bmp);

