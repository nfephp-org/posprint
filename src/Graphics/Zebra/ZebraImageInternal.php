<?php

namespace Posprint\Graphics\Zebra;

use Posprint\Graphics\Zebra\ZebraImageI;

interface ZebraImageInternal extends ZebraImageI
{
    public function getRow($var1);

    public function scaleImage($var1, $var2);

    public function getDitheredB64EncodedPng();
}


