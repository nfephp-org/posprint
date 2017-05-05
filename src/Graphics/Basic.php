<?php

namespace Posprint\Graphics;

/**
 * Basic method for graphics classes
 *
 * @codeCoverageIgnore
 * @category  NFePHP
 * @package   Posprint
 * @copyright Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author    Roberto L. Machado <linux dot rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/posprint for the canonical source repository
 */

abstract class Basic
{
    const LOW = 'low';
    const MEDIUM = 'medium';
    const QUARTILE = 'quartile';
    const HIGH = 'high';
    
    /**
     * Image GD
     *
     * @var resource
     */
    public $img;
    /**
     * Image Height
     *
     * @var int
     */
    protected $imgHeight = 0;
    /**
     * Image Width
     *
     * @var int
     */
    protected $imgWidth = 0;
    
    /**
     * @return int height of the image in pixels
     */
    public function getHeight()
    {
        return $this->imgHeight;
    }
    
    /**
     * @return int Number of bytes to represent a row of this image
     */
    public function getHeightBytes()
    {
        return (int)(($this->imgHeight + 7) / 8);
    }
    
    /**
     * @return int Width of the image
     */
    public function getWidth()
    {
        return $this->imgWidth;
    }
    
    /**
     * @return int Number of bytes to represent a row of this image
     */
    public function getWidthBytes()
    {
        return (int)(($this->imgWidth + 7) / 8);
    }
    
    /**
     * getDimImage
     * Get width and height of resource image
     * and save in properties
     *
     * @return array with dimentions of image
     */
    public function getDimImage()
    {
        if (is_resource($this->img)) {
            $this->imgHeight = imagesy($this->img);
            $this->imgWidth = imagesx($this->img);
        }
        return ['height'  => $this->imgHeight, 'width' => $this->imgWidth];
    }
    
    /**
     * @return boolean True if GD is supported, false otherwise (a wrapper for the version, for mocking in tests)
     */
    protected function isGdSupported()
    {
        return $this->isGdLoaded();
    }

    /**
     * @return boolean True if GD is loaded, false otherwise
     */
    protected function isGdLoaded()
    {
        $gdImgProcessing = extension_loaded('gd');
        return $gdImgProcessing;
    }
    
    /**
     * identifyImg
     * Identifies image file type
     *
     * @param  string $filename
     * @return string
     */
    protected function identifyImg($filename)
    {
        $imgtype = exif_imagetype($filename);
        $aImgTypes = [
            0 => 'NULL',
            1 => 'GIF',
            2 => 'JPEG',
            3 => 'PNG',
            4 => 'SWF',
            5 => 'PSD',
            6 => 'BMP',
            7 => 'TIFF_II',
            8 => 'TIFF_MM',
            9 => 'JPC',
            10 => 'JP2',
            11 => 'JPX',
            12 => 'JB2',
            13 => 'SWC',
            14 => 'IFF',
            15 => 'WBMP',
            16 => 'XBM',
            17 => 'ICO'];
        return $aImgTypes[$imgtype];
    }
}
