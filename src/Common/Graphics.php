<?php

namespace Posprinter\Common;

use Endroid\QrCode\QrCode;
use Intervention\Image\ImageManagerStatic as Image;

class Graphics
{
    public static $img;
    public static $imgHeight;
    public static $imgWidth;

    /**
     * Seta o uso da extensão Imagick ao invés de GD
     */
    public function __construct()
    {
        Image::configure(array('driver' => 'imagick'));
    }

    /**
     * 
     * @param type $resource
     * @param type $width
     * @param type $height
     */
    public static function loadImage($resource, $width = null, $height = null)
    {
        $img = Image::make($resource);
        if ($width != null && $height != null) {
            $img->fit($width, $height);
        } elseif ($width != null || $height != null) {
            $img->resize(
                $width,
                $height,
                function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            );
        }
        self::$img = $img->getCore();
        self::zWHimg();
    }
    
    /**
     * 
     * @return type
     */
    public static function getRaster()
    {
        return self::zGetBinaryImage(self::$img);
    }
    
    /**
     * 
     * @param type $width
     * @param type $height
     */
    public static function resizeImage($width = null, $height = null)
    {
        self::loadImage(self::$img, $width, $height);
    }
    

    /**
     * 
     * @param int $size
     * @param type $padding
     * @param type $errCorretion LOW, MEDIUM, QUARTILE, HIGH
     * @param string $imageType PNG, GIF, JPEG, WBMP
     * @param string $dataText dados do QRCode
     */
    public function createQRCodeImg(
        $width = 200,
        $padding = 10,
        $errCorretion = 'low',
        $dataText = 'NADA NADA NADA'
    ) {
        if ($dataText == '') {
            return;
        }
        $qrCode = new QrCode();
        $qrCode->setText($dataText)
               ->setImageType('png')
               ->setSize($width)
               ->setPadding($padding)
               ->setErrorCorrection($errCorretion)
               ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
               ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
               ->setLabel('')
               ->setLabelFontSize(8);
        self::$img = $qrCode->getImage();
        self::zWHimg();
    }

    /**
     * 
     * @param type $im
     * @return type
     */
    protected static function zGetBinaryImage($objImg)
    {
        $data = "";
        $yMax = imagesx($objImg); // image width
        $xMax = imagesy($objImg); // image height
        for ($xPos = 0; $xPos < $xMax; $xPos++) {
            for ($yPos = 0; $yPos < $yMax; $yPos++) {
                $rgb = imagecolorat($objImg, $yPos, $xPos);
                $red = ($rgb >> 16) & 0xFF;
                $green = ($rgb >> 8) & 0xFF;
                $blue = $rgb & 0xFF;
                $gray = (int)(($red + $green + $blue) / 3);
                $data .= chr($gray);
            }
        }
        return $data;
    }
    
    /**
     * 
     */
    private static function zWHimg()
    {
        self::$imgHeight = imagesy(self::$img);
        self::$imgWidth = imagesx(self::$img);
    }
}
