<?php

namespace Posprint\Graphics;

/**
 * Class Gd2BMP
 * 
 * Originally developed by James Heinrich
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

class Gd2BMP
{
    /**
     * GD resource
     * @var GD
     */
    private static $img;
    
    /**
     * Construct
     * May be recieve a GD image resource or not
     * @param resource $gdimage
     */
    public function __construct($gdimage = null)
    {
        self::loadImg($gdimage);
    }
    
    /**
     * Loads the GD image to parameter class
     * @param resource $gdimage
     */
    private static function loadImg($gdimage = null)
    {
        if (is_resource($gdimage)) {
            self::$img = $gdimage;
        }
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
