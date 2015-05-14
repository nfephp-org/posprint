<?php

namespace Posprint\Common;

use Endroid\QrCode\QrCode;
use Exception;

class Graphics
{
    public static $img;
    public static $imgHeight;
    public static $imgWidth;

    /**
     * 
     */
    public function __construct($filename = '', $width = null, $height = null)
    {
        self::loadImage($filename, $width, $height);
    }

    /**
     * 
     * @param type $resource
     * @param type $width
     * @param type $height
     */
    public static function loadImage($filename, $width = null, $height = null)
    {
        if (! is_file($filename)) {
            return;
        }
        if (!is_readable($filename)) {
            throw new Exception("Não é possivel ler esse arquivo '$filename' Permissões!!");
        }
        $tipo = self::zIdentifyImg($filename);
        $func = 'imagecreatefrom' . strtolower($tipo);
        if (! function_exists($func)) {
            throw new Exception("Não é possivel usar ou tratar esse tipo de imagem, com GD");
        }
        self::$img = $func($filename);
        if (! self::$img) {
            throw new Exception("Falhou ao carregar a imagem '$filename'.");
        }
        self::zLoadDimImage();
        if ($width != null || $height != null) {
            self::resizeImage($width, $height);
        }
    }
    
    /**
     * 
     * @return type
     */
    public static function getImageRaster()
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
        if ($width != null && $height == null) {
            $razao = $width / self::$imgWidth;
            $height = (int) round($razao * self::$imgHeight, 0);
        } elseif ($width == null && $height != null) {
            $razao = $height / self::$imgHeight;
            $width = (int) round($razao * self::$imgWidth, 0);
        }
        $tempimg = imagecreatetruecolor($width, $height);
        imagecopyresampled($tempimg, self::$img, 0, 0, 0, 0, $width, $height, self::$imgWidth, self::$imgHeight);
        self::$img = $tempimg;
        self::zLoadDimImage();
    }

    /**
     * 
     * @param int $size
     * @param type $padding
     * @param type $errCorretion LOW, MEDIUM, QUARTILE, HIGH
     * @param string $imageType PNG, GIF, JPEG, WBMP
     * @param string $dataText dados do QRCode
     */
    public static function getImageQRCode(
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
        self::zLoadDimImage();
    }

    public static function getImage()
    {
        return self::$img;
    }
    
    /**
     * 
     * @return type
     */
    protected static function zGetBinaryImage()
    {
        $data = "";
        for ($yPos = 0; $yPos < self::$imgHeight; $yPos++) {
            for ($xPos = 0; $xPos < self::$imgWidth; $xPos++) {
                $rgb = imagecolorat(self::$img, $xPos, $yPos);
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
    private static function zLoadDimImage()
    {
        self::$imgHeight = imagesy(self::$img);
        self::$imgWidth = imagesx(self::$img);
    }
    
    /**
     * zIdentifyImg
     * @param string $filename
     * @return string
     */
    private static function zIdentifyImg($filename)
    {
        $imgtype = exif_imagetype($filename);
        switch ($imgtype) {
            case 1:
                $typo = 'GIF';
                break;
            case 2:
                $typo = 'JPEG';
                break;
            case 3:
                $typo = 'PNG';
                break;
            case 15:
                $typo = 'WBMP';
                break;
            default:
                $typo = 'none';
        }
        return $typo;
    }
}
