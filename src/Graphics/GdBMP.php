<?php

namespace Posprint\Graphics;

/**
 * Class GdBMP
 * 
 * Originally developed by James Heinrich
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux dot rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use InvalidArgumentException;
use Posprint\Graphics\Basic;

class GdBMP extends Basic
{
    /**
     * GD resource
     * @var GD
     */
    private static $img;
    
    /**
     * Construct
     * May be recieve a GD image resource or a BMP image file path
     * @param resource|string $image
     */
    public function __construct($image = null)
    {
        self::loadImg($image);
    }
    
    
    /**
     * Convert a GD image into a BMP string representation
     * @param resource $gdimage is a GD image
     * @return type
     */
    public static function convert($gdimage = null)
    {
        self::loadImg($gdimage);
        
        if (! is_resource(self::$img)) {
            return '';
        }
        
        $imageX = ImageSX(self::$img);
        $imageY = ImageSY(self::$img);

        $bmp = '';
        for ($y = ($imageY - 1); $y >= 0; $y--) {
            $thisline = '';
            for ($x = 0; $x < $imageX; $x++) {
                $argb = self::getPixelColor(self::$img, $x, $y);
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
        return $bitMapHeader.$bitMapInfoHeader.$bmp;
    }
    

    /**
     * loadBMP
     * Create a GD image from BMP file
     * @param string $filename
     * @return GD object
     */
    public static function loadBMP($filename)
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
        self::$img = $image;
        return $image;
    }
    
    /**
     * Loads the GD image to parameter class
     * @param resource|string $image GD resource or path image file
     */
    private static function loadImg($image = null)
    {
        if (is_null($image)) {
            return;
        }
        if (is_resource($image)) {
            self::$img = $image;
            return;
        }
        //check for image path
        if(! is_file($image)) {
           throw new InvalidArgumentException("Image file not found.");
        }
        $imgtype = $this->identifyImg($image);
        if ($imgtype == 'BMP') {
            $this->loadBMP($image);
        }
    }
    
    /**
     * Converts Litte Endian Bytes do String
     * @param int $number
     * @param int $minbytes
     * @return string
     */
    private static function littleEndian2String($number, $minbytes=1)
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
     * @param resource $img
     * @param int $x
     * @param intr $y
     * @return array
     */
    private static function getPixelColor($img, $x, $y)
    {
        return imageColorsForIndex($img, imageColorAt($img, $x, $y));
    }
}
