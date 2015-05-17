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
        if ($tipo == 'BMP') {
            self::$img = self::imageCreateFrombmp($filename);
        } else {
            $func = 'imagecreatefrom' . strtolower($tipo);
            if (! function_exists($func)) {
                throw new Exception("Não é possivel usar ou tratar esse tipo de imagem, com GD");
            }
            self::$img = $func($filename);
        }
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
            case 6:
                $typo = 'BMP';
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
    
    /**
     * imageCreateFrombmp
     * Create a GD image from BMP file
     * @param string $filename
     * @return GD object
     */
    private static function imageCreateFrombmp($filename)
    {
        //Load the image into a string
        $file = fopen($filename, "rb");
        $read = fread($file, 10);
        //continue at the end of file
        while (! feof($file) && ($read <> "")) {
            $read .= fread($file, 1024);
        }
        fclose($file);
        $temp = unpack("H*", $read);
        $hex = $temp[1];
        $header = substr($hex, 0, 108);
        //Process the header
        //Structure: http://www.fastgraph.com/help/bmp_header_format.html
        if (substr($header, 0, 4) != "424d") {
            //is not a BMP file
            return false;
        }
        //Cut it in parts of 2 bytes
        $headerParts = str_split($header, 2);
        //Get the width 4 bytes
        $width = hexdec($headerParts[19].$headerParts[18]);
        //Get the height 4 bytes
        $height = hexdec($headerParts[23].$headerParts[22]);
        // Unset the header params
        unset($headerParts);
        // Define starting X and Y
        $xPos = 0;
        $yPos = 1;
        //Create new gd image
        $image = imagecreatetruecolor($width, $height);
        //Grab the body from the image
        $body = substr($hex, 108);
        //Calculate if padding at the end-line is needed
        //Divided by two to keep overview.
        //1 byte = 2 HEX-chars
        $bodySize = (strlen($body) / 2);
        $headerSize = ($width * $height);
        //Use end-line padding? Only when needed
        $usePadding = ($bodySize > ($headerSize * 3) + 4);
        //Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
        //Calculate the next DWORD-position in the body
        for ($iCount = 0; $iCount < $bodySize; $iCount += 3) {
            //Calculate line-ending and padding
            if ($xPos >= $width) {
                //If padding needed, ignore image-padding
                //Shift i to the ending of the current 32-bit-block
                if ($usePadding) {
                    $iCount += $width % 4;
                }
                //Reset horizontal position
                $xPos = 0;
                //Raise the height-position (bottom-up)
                $yPos++;
                //Reached the image-height? Break the for-loop
                if ($yPos > $height) {
                    break;
                }
            }
            //Calculation of the RGB-pixel (defined as BGR in image-data)
            //Define $i_pos as absolute position in the body
            $iPos = $iCount * 2;
            $red = hexdec($body[$iPos + 4] . $body[$iPos + 5]);
            $green = hexdec($body[$iPos + 2] . $body[$iPos + 3]);
            $blue = hexdec($body[$iPos] . $body[$iPos + 1]);
            //Calculate and draw the pixel
            $color = imagecolorallocate($image, $red, $green, $blue);
            imagesetpixel($image, $xPos, $height-$yPos, $color);
            //Raise the horizontal position
            $xPos++;
        }
        //Unset the body / free the memory
        unset($body);
        //Return image-object
        return $image;
    }
}
