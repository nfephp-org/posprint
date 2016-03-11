<?php

namespace Posprint\Graphics;

/**
 * Classe Graphics
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux dot rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Graphics\Basic;
use Endroid\QrCode\QrCode;
use RuntimeException;
use InvalidArgumentException;

class Graphics extends Basic
{
    /**
     * Image prixels in BW
     * @var string 
     */
    protected $imgData = null;
    /**
     * Image Raster bit
     * @var string
     */
    protected $imgRasterData = null;
  
    /**
     * Constructor 
     * Load a image, if passed a path to file and adjust dimentions
     * 
     * @param string $filename
     * @param int $width
     * @param int $height
     * @throws RuntimeException
     */
    public function __construct($filename = null, $width = null, $height = null)
    {
        // Include option to Imagick
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
     * @param string $filename path to image file
     * @param float $width 
     * @param float $height
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
            $this->img = $img; 
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
     * Save image to PNG file 
     * @param string $filename
     */
    public function save($filename = null)
    {
        $this->saveImage($filename, $this->img);
    }
    
    /**
     * Convert a GD image into a BMP string representation
     * @param string $filename path to image BMP file
     * @return string
     */
    public function convert2BMP($filename = null)
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
                //change transparency to white color
                if ($argb['alpha'] == 0 && $argb['blue'] == 0 && $argb['green'] == 0 && $argb['red'] == 0) {
                    $thisline .= chr(255).chr(255).chr(255);
                } else {
                    $thisline .= chr($argb['blue']).chr($argb['green']).chr($argb['red']);
                }    
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
     * loadBMP
     * Create a GD image from BMP file
     * @param string $filename
     * @return GD object
     */
    public function loadBMP($filename)
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
        //Return GD image-object
        $this->img = $image;
        return $image;
    }
    
    /**
     * resizeImage
     * Resize an image
     * NOTE: the width is always set to the multiple of 8 more 
     * next, why? printers have a resolution of 8 dots per mm
     * 
     * @param float $width
     * @param float $height
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
     * @param int $width
     * @param int $padding
     * @param string $errCorretion
     */
    public function imageQRCode(
        $dataText = 'NADA NADA NADA NADA NADA NADA NADA NADA NADA NADA NADA NADA',
        $width = 200,
        $padding = 10,
        $errCorretion = 'medium'
    ) {
        //adjust width for a closest multiple of 8
        $width = $this->closestMultiple($width, 8);
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
        $this->img = $qrCode->getImage();
        $this->getDimImage();
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
     * @throws RuntimeException Where the generated data is unsuitable for the printer (indicates a bug or oversized image).
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
            $byteVal |= (int) $this->imgData[$yCount * $widthPixels + $xCount] << (7 - $bit);
            $xCount++;
            $bit++;
            if ($xCount >= $widthPixels) {
                $xCount = 0;
                $yCount++;
                $bit = 8;
                if($yCount >= $heightPixels) {
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
     * @param string $filename
     * @param resource|string|null $data
     * @return boolean
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    private function saveImage($filename = null, $data = null)
    {
        if (is_null($filename) || is_null($data)) {
            return false;
        }
        if (is_resource($data)) {
            //use GD to save image to file
            $result = imagepng($data, $filename);
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
     * Converts Litte Endian Bytes do String
     * 
     * @param int $number
     * @param int $minbytes
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
     * @param resource $img
     * @param int $x
     * @param int $y
     * @return array
     */
    private static function getPixelColor($img, $x, $y)
    {
        return imageColorsForIndex($img, imageColorAt($img, $x, $y));
    }
    
    /**
     * Ajusta o numero para o multiplo mais proximo de base
     * 
     * @param float $num
     * @param int $num
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
