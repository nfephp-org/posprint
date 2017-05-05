<?php

namespace Posprint\Graphics;

/**
 * Classe Graphics
 *
 * @category  NFePHP
 * @package   Posprint
 * @copyright Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author    Roberto L. Machado <linux dot rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Graphics\Basic;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use RuntimeException;
use InvalidArgumentException;

class Graphics extends Basic
{
    /**
     * Image prixels in BW
     *
     * @var string
     */
    protected $imgData = null;
    /**
     * Image Raster bit
     *
     * @var string
     */
    protected $imgRasterData = null;
  
    /**
     * Constructor
     * Load a image, if passed a path to file and adjust dimentions
     *
     * @param  string $filename
     * @param  int    $width
     * @param  int    $height
     * @throws RuntimeException
     */
    public function __construct($filename = null, $width = null, $height = null)
    {
        if (! $this->isGdSupported()) {
            throw new RuntimeException("GD module not found.");
        }
        $this->imgHeight = 0;
        $this->imgWidth = 0;
        $this->imgData = null;
        $this->imgRasterData = null;
        // Load the image, if the patch was passed
        if (! is_null($filename)) {
            $this->load($filename, $width, $height);
        }
    }
    
    /**
     * Return a string of bytes
     * for inclusion on printer commands
     * This method change image to Black and White and
     * reducing the color resolution of 1 bit per pixel
     *
     * @return string
     */
    public function getRasterImage()
    {
        $this->resizeImage($this->imgWidth);
        $this->convertPixelBW();
        $this->convertRaster();
        return $this->imgRasterData;
    }

    /**
     * load
     * Load image file and adjust dimentions
     *
     * @param  string $filename path to image file
     * @param  float  $width
     * @param  float  $height
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function load($filename, $width = null, $height = null)
    {
        if (! is_file($filename)) {
            throw new InvalidArgumentException("Image file not found.");
        }
        if (! is_readable($filename)) {
            throw new RuntimeException("The file can not be read due to lack of permissions.");
        }
        //identify type of image and load with GD
        $tipo = $this->identifyImg($filename);
        if ($tipo == 'BMP') {
            $img = $this->loadBMP($filename);
            if ($img === false) {
                throw new InvalidArgumentException("Image file is not a BMP");
            }
        } else {
            $func = 'imagecreatefrom' . strtolower($tipo);
            if (! function_exists($func)) {
                throw new RuntimeException("It is not possible to use or handle this type of image with GD");
            }
            $this->img = $func($filename);
        }
        if (! $this->img) {
            throw new RuntimeException("Failed to load image '$filename'.");
        }
        //get image dimentions
        $this->getDimImage();
        if ($width != null || $height != null) {
            $this->resizeImage($width, $height);
        }
    }
    
    /**
     * Converts a true color image to Black and white
     * even if the image have transparency (alpha channel)
     */
    public function convertBW()
    {
        $newimg = imagecreatetruecolor($this->imgWidth, $this->imgHeight);
        imagealphablending($newimg, false);
        imagesavealpha($newimg, true);
        imagecopyresampled(
            $newimg,
            $this->img,
            0,
            0,
            0,
            0,
            $this->imgWidth,
            $this->imgHeight,
            $this->imgWidth,
            $this->imgHeight
        );
        $bcg = imagecolorallocate($newimg, 255, 255, 255);
        imagefill($newimg, 0, 0, $bcg);
        imagefilter($newimg, IMG_FILTER_GRAYSCALE);
        imagefilter($newimg, IMG_FILTER_CONTRAST, -1000);
        $this->img = $newimg;
    }
    
    /**
     * Save image to file
     *
     * @param string $filename
     * @param string $type  PNG, JPG, GIF, BMP
     * @param integer $quality 0 - 100 default 75
     * @return boolean
     */
    public function save($filename = null, $type = 'PNG', $quality = 75)
    {
        $type = strtoupper($type);
        if ($type == 'BMP') {
            $this->saveBMP($filename);
            return true;
        }
        $aTypes = ['PNG', 'JPG', 'JPEG',  'GIF'];
        if (! in_array($type, $aTypes)) {
            throw InvalidArgumentException('This file type is not supported.');
        }
        return $this->saveImage($filename, $this->img, $type, $quality);
    }
    
    /**
     * resizeImage
     * Resize an image
     * NOTE: the width is always set to the multiple of 8 more
     * next, why? printers have a resolution of 8 dots per mm
     *
     * @param  float $width
     * @param  float $height
     * @throws InvalidArgumentException
     */
    public function resizeImage($width = null, $height = null)
    {
        if ($width == null && $height == null) {
            throw new InvalidArgumentException("No dimensions was passed.");
        }
        if ($width != null) {
            $width = $this->closestMultiple($width);
            $razao = $width / $this->imgWidth;
            $height = (int) round($razao * $this->imgHeight);
        } elseif ($width == null && $height != null) {
            $razao = $height / $this->imgHeight;
            $width = (int) round($razao * $this->imgWidth);
            $width = $this->closestMultiple($width);
        }
        $tempimg = imagecreatetruecolor($width, $height);
        imagecopyresampled($tempimg, $this->img, 0, 0, 0, 0, $width, $height, $this->imgWidth, $this->imgHeight);
        $this->img = $tempimg;
        $this->getDimImage();
    }
    
    /**
     * Creates a  GD QRCode image
     *
     * @param string $dataText
     * @param int    $width
     * @param int    $padding
     * @param string $errCorretion  LOW, MEDIUM, QUARTILE, HIGH
     * @return void
     */
    public function imageQRCode(
        $dataText = 'NADA NADA NADA NADA NADA NADA NADA NADA NADA NADA NADA NADA',
        $width = 200,
        $padding = 10,
        $errCorretion = 'MEDIUM'
    ) {
        //adjust width for a closest multiple of 8
        $width = $this->closestMultiple($width, 8);
        //create image
        try {
            $qrCode = new QrCode($dataText);
            $qrCode->setMargin($padding);
            $qrCode->setSize($width);
            $qrCode->setEncoding('UTF-8');
            $qrCode->setErrorCorrectionLevel(strtolower($errCorretion));
            $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0]);
            $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255]);
            $qrCode->setValidateResult(true);
            //write PNG image
            $this->img = imagecreatefromstring(
                $qrCode->writeString(PngWriter::class)
            );
            $this->getDimImage();
        } catch (\Exception $e) {
            throw new \RuntimeException(
                "ERROR. Falha de validação Ajuste o tamanho do codigo "
                . "para permitir legibilidade. [" . $e->getMessage() . ']'
            );
        }
    }
    
    /**
     * loadBMP
     * Create a GD image from BMP file
     *
     * @param  string $filename
     * @return boolean
     */
    protected function loadBMP($filename)
    {
        //open file as binary
        if (! $f1 = fopen($filename, "rb")) {
            throw InvalidArgumentException('Can not open file.');
        }
        //get properties from image file
        $file = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
        if ($file['file_type'] != 19778) {
            throw InvalidArgumentException('This file is not a BMP image.');
        }
        //get properties form image
        $bmp = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
           '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
           '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
        //check deep of colors
        $bmp['colors'] = pow(2, $bmp['bits_per_pixel']);
        if ($bmp['size_bitmap'] == 0) {
            $bmp['size_bitmap'] = $file['file_size'] - $file['bitmap_offset'];
        }
        $bmp['bytes_per_pixel'] = $bmp['bits_per_pixel']/8;
        $bmp['bytes_per_pixel2'] = ceil($bmp['bytes_per_pixel']);
        $bmp['decal'] = ($bmp['width']*$bmp['bytes_per_pixel']/4);
        $bmp['decal'] -= floor($bmp['width']*$bmp['bytes_per_pixel']/4);
        $bmp['decal'] = 4-(4*$bmp['decal']);
        if ($bmp['decal'] == 4) {
            $bmp['decal'] = 0;
        }
        $palette = array();
        if ($bmp['colors'] < 16777216) {
            $palette = unpack('V'.$bmp['colors'], fread($f1, $bmp['colors']*4));
        }
        //read all data form image but not the header
        $img = fread($f1, $bmp['size_bitmap']);
        fclose($f1);
        //create a true color GD resource
        $vide = chr(0);
        $res = imagecreatetruecolor($bmp['width'], $bmp['height']);
        $p = 0;
        $y = $bmp['height']-1;
        //read all bytes form original file
        while ($y >= 0) {
            $x=0;
            while ($x < $bmp['width']) {
                //get byte color from BMP
                $color = $this->getBMPColor($bmp['bits_per_pixel'], $img, $vide, $p, $palette);
                if ($color === false) {
                    throw RuntimeException('Fail during conversion from BMP number bit per pixel incorrect!');
                }
                imagesetpixel($res, $x, $y, $color[1]);
                $x++;
                $p += $bmp['bytes_per_pixel'];
            }
            $y--;
            $p += $bmp['decal'];
        }
        $this->img = $res;
        return true;
    }

    /**
     * Convert a GD image into a BMP string representation
     *
     * @param string $filename
     * @return string
     */
    protected function saveBMP($filename = null)
    {
        if (! is_resource($this->img)) {
            return '';
        }
        //to remove alpha color and put white instead
        $img = $this->img;
        $imageX = imagesx($img);
        $imageY = imagesy($img);
        $bmp = '';
        for ($yInd = ($imageY - 1); $yInd >= 0; $yInd--) {
            $thisline = '';
            for ($xInd = 0; $xInd < $imageX; $xInd++) {
                $argb = self::getPixelColor($img, $xInd, $yInd);
                $thisline .= chr($argb['blue']).chr($argb['green']).chr($argb['red']);
            }
            while (strlen($thisline) % 4) {
                $thisline .= "\x00";
            }
            $bmp .= $thisline;
        }
        $bmpSize = strlen($bmp) + 14 + 40;
        // bitMapHeader [14 bytes] - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_62uq.asp
        $bitMapHeader  = 'BM'; // WORD bfType;
        $bitMapHeader .= self::littleEndian2String($bmpSize, 4); // DWORD bfSize;
        $bitMapHeader .= self::littleEndian2String(0, 2); // WORD bfReserved1;
        $bitMapHeader .= self::littleEndian2String(0, 2); // WORD bfReserved2;
        $bitMapHeader .= self::littleEndian2String(54, 4); // DWORD bfOffBits;
        // bitMapInfoHeader - [40 bytes] http://msdn.microsoft.com/library/en-us/gdi/bitmaps_1rw2.asp
        $bitMapInfoHeader  = self::littleEndian2String(40, 4); // DWORD biSize;
        $bitMapInfoHeader .= self::littleEndian2String($imageX, 4); // LONG biWidth;
        $bitMapInfoHeader .= self::littleEndian2String($imageY, 4); // LONG biHeight;
        $bitMapInfoHeader .= self::littleEndian2String(1, 2); // WORD biPlanes;
        $bitMapInfoHeader .= self::littleEndian2String(24, 2); // WORD biBitCount;
        $bitMapInfoHeader .= self::littleEndian2String(0, 4); // DWORD biCompression;
        $bitMapInfoHeader .= self::littleEndian2String(0, 4); // DWORD biSizeImage;
        $bitMapInfoHeader .= self::littleEndian2String(2835, 4); // LONG biXPelsPerMeter;
        $bitMapInfoHeader .= self::littleEndian2String(2835, 4); // LONG biYPelsPerMeter;
        $bitMapInfoHeader .= self::littleEndian2String(0, 4); // DWORD biClrUsed;
        $bitMapInfoHeader .= self::littleEndian2String(0, 4); // DWORD biClrImportant;
        $data = $bitMapHeader.$bitMapInfoHeader.$bmp;
        $this->saveImage($filename, $data);
        return $data;
    }
    
    /**
     * Convert image from GD resource
     * into Black and White pixels image
     *
     * @return string Representation of bytes image in BW
     */
    protected function convertPixelBW()
    {
        // Make a string of 1's and 0's
        $this->imgData = str_repeat("\0", $this->imgHeight * $this->imgWidth);
        for ($yInd = 0; $yInd < $this->imgHeight; $yInd++) {
            for ($xInd = 0; $xInd < $this->imgWidth; $xInd++) {
                //get colors from byte image
                $cols = imagecolorsforindex($this->img, imagecolorat($this->img, $xInd, $yInd));
                //convert to greyness color 1 for white, 0 for black
                $greyness = (int)(($cols['red'] + $cols['green'] + $cols['blue']) / 3) >> 7;
                //switch to Black and white
                //1 for black, 0 for white, taking into account transparency color
                $black = (1 - $greyness) >> ($cols['alpha'] >> 6);
                $this->imgData[$yInd * $this->imgWidth + $xInd] = $black;
            }
        }
        return $this->imgData;
    }

    /**
     * Output the image in raster (row) format.
     * This can result in padding on the right of the image,
     * if its width is not divisible by 8.
     *
     * @throws RuntimeException Where the generated data is
     *         unsuitable for the printer (indicates a bug or oversized image).
     * @return string The image in raster format.
     */
    protected function convertRaster()
    {
        if (! is_null($this->imgRasterData)) {
             return $this->imgRasterData;
        }
        if (is_null($this->imgData)) {
            $this->convertPixelBW();
        }
        //get width in Pixels
        $widthPixels = $this->getWidth();
        //get heightin in Pixels
        $heightPixels = $this->getHeight();
        //get width in Bytes
        $widthBytes = $this->getWidthBytes();
        //initialize vars
        $xCount = $yCount = $bit = $byte = $byteVal = 0;
        //create a string for converted bytes
        $data = str_repeat("\0", $widthBytes * $heightPixels);
        if (strlen($data) == 0) {
            return $data;
        }
        /* Loop through and convert format */
        do {
            $num = $yCount * $widthPixels + $xCount;
            $byteVal |= (int) $this->imgData[$num] << (7 - $bit);
            $xCount++;
            $bit++;
            if ($xCount >= $widthPixels) {
                $xCount = 0;
                $yCount++;
                $bit = 8;
                if ($yCount >= $heightPixels) {
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
        if (strlen($data) != ($this->getWidthBytes() * $this->getHeight())) {
            throw new RuntimeException("Bug in " . __FUNCTION__ . ", wrong number of bytes.");
        }
         $this->imgRasterData = $data;
         return $this->imgRasterData;
    }
    
    /**
     * Save safety binary image file
     *
     * @param string               $filename
     * @param resource|string|null $data
     * @param string $type PNG, JPG, GIF, BMP
     * @param integer $quality
     * @return boolean
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function saveImage($filename = null, $data = null, $type = 'PNG', $quality = 75)
    {
        if (empty($filename) || empty($data)) {
            return false;
        }
        if (is_resource($data)) {
            //use GD to save image to file
            switch ($type) {
                case 'JPG':
                case 'JPEG':
                    $result = imagejpeg($data, $filename, $quality);
                    break;
                case 'GIF':
                    $result = imagegif($data, $filename);
                    break;
                default:
                    $result = imagepng($data, $filename);
                    break;
            }
            if (!$result) {
                throw new InvalidArgumentException("Fail to write in $filename.");
            }
            return true;
        }
        $handle = @fopen($filename, 'w');
        if (!is_resource($handle)) {
            throw new InvalidArgumentException("Cant open file $filename. Check permissions.");
        }
        $nbytes = fwrite($handle, $data);
        fclose($handle);
        if (!$nbytes) {
            throw new RuntimeException("Fail to write in $filename.");
        }
        return true;
    }

    /**
     * Get byte color form BMP
     *
     * @param integer $bpp bytes_per_pixel
     * @param string $img bytes read of file
     * @param string $vide
     * @param integer $p
     * @param integer $palette
     * @return integer|boolean
     */
    private function getBMPColor($bpp, $img, $vide, $p, $palette)
    {
        switch ($bpp) {
            case 24:
                return unpack("V", substr($img, $p, 3).$vide);
                break;
            case 16:
                $color = unpack("v", substr($img, $p, 2));
                $blue = ($color[1] & 0x001f) << 3;
                $green = ($color[1] & 0x07e0) >> 3;
                $red = ($color[1] & 0xf800) >> 8;
                $color[1] = $red * 65536 + $green * 256 + $blue;
                return $color;
                break;
            case 8:
                $color = unpack("n", $vide.substr($img, $p, 1));
                $color[1] = $palette[$color[1]+1];
                return $color;
                break;
            case 4:
                $color = unpack("n", $vide.substr($img, floor($p), 1));
                if (($p*2)%2 == 0) {
                    $color[1] = ($color[1] >> 4) ;
                } else {
                    $color[1] = ($color[1] & 0x0F);
                }
                $color[1] = $palette[$color[1]+1];
                return $color;
                break;
            case 1:
                $color = unpack("n", $vide.substr($img, floor($p), 1));
                if (($p*8)%8 == 0) {
                    $color[1] = $color[1]>>7;
                } elseif (($p*8)%8 == 1) {
                    $color[1] = ($color[1] & 0x40)>>6;
                } elseif (($p*8)%8 == 2) {
                    $color[1] = ($color[1] & 0x20)>>5;
                } elseif (($p*8)%8 == 3) {
                    $color[1] = ($color[1] & 0x10)>>4;
                } elseif (($P*8)%8 == 4) {
                    $color[1] = ($color[1] & 0x8)>>3;
                } elseif (($p*8)%8 == 5) {
                    $color[1] = ($color[1] & 0x4)>>2;
                } elseif (($p*8)%8 == 6) {
                    $color[1] = ($color[1] & 0x2)>>1;
                } elseif (($p*8)%8 == 7) {
                    $color[1] = ($color[1] & 0x1);
                }
                $color[1] = $palette[$color[1]+1];
                return $color;
                break;
            default:
                return false;
        }
    }
    
    /**
     * Converts Litte Endian Bytes do String
     *
     * @param  int $number
     * @param  int $minbytes
     * @return string
     */
    private static function littleEndian2String($number, $minbytes = 1)
    {
        $intstring = '';
        while ($number > 0) {
            $intstring = $intstring.chr($number & 255);
            $number >>= 8;
        }
        return str_pad($intstring, $minbytes, "\x00", STR_PAD_RIGHT);
    }
    
    /**
     * Get pixel colors
     *
     * @param  resource $img
     * @param  int      $x
     * @param  int      $y
     * @return array
     */
    private static function getPixelColor($img, $x, $y)
    {
        return imageColorsForIndex($img, imageColorAt($img, $x, $y));
    }
    
    /**
     * Ajusta o numero para o multiplo mais proximo de base
     *
     * @param  float $num
     * @param  int   $num
     * @return int
     */
    private function closestMultiple($num = 0, $base = 8)
    {
        $iNum = ceil($num);
        if (($iNum % $base) === 0) {
            return $iNum;
        }
        return round($num/$base) * $base;
    }
}
