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
     * Construtor 
     * Carrega uma imagem, se for passada e ajusta suas dimensões
     * 
     * @param string $filename
     * @param int $width
     * @param int $height
     */
    public function __construct($filename = '', $width = null, $height = null)
    {
        self::loadImage($filename, $width, $height);
    }

    /**
     * loadImage
     * Carrega uma imagem, se for passada e ajusta suas dimensões
     * @param type $resource
     * @param type $width
     * @param type $height
     */
    public static function loadImage($filename, $width = null, $height = null)
    {
        if (! is_file($filename)) {
            return;
        }
        if (! is_readable($filename)) {
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
     * getWidth
     * Retorna a latgura em pixels
     * @return int
     */
    public static function getWidth()
    {
        return self::$imgWidth;
    }
    
    /**
     * getHeight
     * Retorna a altura em pixels
     * @return int
     */
    public static function getHeight()
    {
        return self::$imgHeight;
    }
    
    /**
     * getImageBinary
     * Retorna a representação em Bytes da imagem
     * @return type
     */
    public static function getImageBinary()
    {
        return self::zPixel2Byte();
    }
    
    /**
     * resizeImage
     * Redimensiona uma imagem
     * NOTA: a largura será sempre ajustada para o multiplo de 8 mais
     * proximo, por que as impressoras tem uma resolução de 8 dots
     * @param int $width
     * @param int $height
     */
    public static function resizeImage($width = null, $height = null)
    {
        if ($width == null && $height == null) {
            return;
        }
        if ($width != null && $height == null) {
            $width = self::zAdjustMultiple($width);
            $razao = $width / self::$imgWidth;
            $height = (int) round($razao * self::$imgHeight);
        } elseif ($width == null && $height != null) {
            $razao = $height / self::$imgHeight;
            $width = (int) round($razao * self::$imgWidth);
            $width = self::zAdjustMultiple($width);
        } elseif ($width != null && $height != null) {
            $width = self::zAdjustMultiple($width);
        }
        $tempimg = imagecreatetruecolor($width, $height);
        imagecopyresampled($tempimg, self::$img, 0, 0, 0, 0, $width, $height, self::$imgWidth, self::$imgHeight);
        self::$img = $tempimg;
        self::zLoadDimImage();
    }
    
    /**
     * getImageQRCode
     * Gera uma imagem GD do QRCode 
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
        $width = self::zAdjustMultiple($width);
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
    
    /**
     * getGDobj
     * Retorna um objeto GD
     * @return GD
     */
    public static function getGDobj()
    {
        return self::$img;
    }
    
    /**
     * zPixel2Byte
     * Converte cada pixel da imagem em um byte,
     * cada byte irão representar 8 dots 
     * @return array
     * @throws Exception
     */
    protected static function zPixel2Byte()
    {
        $widthPixels = self::$imgWidth;
        $heightPixels = self::$imgHeight;
        //numero de bytes da imagem
        $widthBytes = (int)((self::$imgWidth + 7) / 8);
        $imgData = self::zPixel2BitBW(self::$img, $widthPixels, $heightPixels);
        $xPos = $yPos = $bit = $byte = $byteVal = 0;
        $data = str_repeat("\0", $widthBytes * $heightPixels);
        do {
            $byteVal |= (int) $imgData[$yPos * $widthPixels + $xPos] << (7 - $bit);
            $xPos++;
            $bit++;
            if ($xPos >= $widthPixels) {
                $xPos = 0;
                $yPos++;
                $bit = 8;
                if ($yPos >= $heightPixels) {
                    $data[$byte] = chr($byteVal);
                    break;
                }
            }
            if ($bit >= 8) {
                $data[$byte] = chr($byteVal);
                $byteVal = 0;
                $bit = 0;
                $byte++;
            }
        } while (true);
        return $data;
    }
    
    /**
     * zPixel2BitBW
     * Converte a imagem em uma representação de seus bits com a 
     * imagem convertida para BW
     * @param GD $img
     * @param int $widthPixels
     * @param int $heightPixels
     * @return array
     */
    private static function zPixel2BitBW($img, $widthPixels, $heightPixels)
    {
        //cria uma matriz com zeros hex que representa uma imagem em branco
        $imgData = str_repeat("\0", $heightPixels * $widthPixels);
        for ($yPos = 0; $yPos < $heightPixels; $yPos++) {
            for ($xPos = 0; $xPos < $widthPixels; $xPos++) {
                //pega a cor do pixel da imagem e converte em cinza
                $colors = imagecolorsforindex($img, imagecolorat($img, $xPos, $yPos));
                $greyness = (int)($colors['red'] + $colors['red'] + $colors['blue']) / 3;
                $black = (255 - $greyness) >> (7 + ($colors['alpha'] >> 6));
                //carrega a matriz com o tom de cinza
                $imgData[$yPos * $widthPixels + $xPos] = $black;
            }
        }
        return $imgData;
    }

    /**
     * zLoadDimImage
     * Obtêm a largura e a altura da imagem em pixels e 
     * colo nas propriedades da classe
     */
    private static function zLoadDimImage()
    {
        self::$imgHeight = imagesy(self::$img);
        self::$imgWidth = imagesx(self::$img);
    }
    
    /**
     * zIdentifyImg
     * Identifica o tipo de imagem
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
    
    /**
     * zAdjustMultiple
     * Ajusta o numero para o multipo mais proximo de 8
     * @param int $num
     * @return int
     */
    private static function zAdjustMultiple($num = 0, $base = 8)
    {
        //caso a largura da imagem em pixels não seja multiplo de $base
        //converter no multiplo de 8 mais proximo
        if ($num % $base != 0) {
            $num = round($num/$base) * $base;
        }
        return $num;
    }
}
