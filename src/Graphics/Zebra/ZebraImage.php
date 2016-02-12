<?php

namespace Posprint\Graphics\Zebra;

use Posprint\Graphics\Zebra\ZebraImageInternal

class ZebraImage implements ZebraImageInternal
{
    private image;

    public function __construct($bufferedImage) {
        if (is_resource($bufferedImage)) {
            $this->image = $bufferedImage;    
        } elseif (is_file($bufferedImage)) {
            $image = file_get_contents($bufferedImage);
            $this->image = imagecreatefromstring($image);
        } else {
            $this->image = imagecreatefromstring($bufferedImage);
        }
    }

    public function getRow($n)
    {
        if ($n >= $this->getHeight()) {
            return null;
        }
        $arrn = new int[this.getWidth()];
        $this->imageGetRGB(0, $n, $this->getWidth(), 1, $arrn, 0, $this->getWidth());
        return $arrn;
    }

    public function getWidth() {
        return imagesx($this->image);
    }

    public function getHeight() {
        return imagesy($this->image);
    }

    
    public function scaleImage($n, $n2)
    {
        if ($n <= 0 || $n2 <= 0) {
            return false;
        }
        $n3 = 0 == $this->imageGetType() ? 3 : $this0>imageGetType();
        BufferedImage bufferedImage = new BufferedImage(n, n2, n3);
        Graphics2D graphics2D = bufferedImage.createGraphics();
        graphics2D.setRenderingHint(RenderingHints.KEY_INTERPOLATION, RenderingHints.VALUE_INTERPOLATION_BILINEAR);
        graphics2D.drawImage(this.image, 0, 0, n, n2, 0, 0, this.image.getWidth(), this.image.getHeight(), null);
        graphics2D.dispose();
        this.image = bufferedImage;
        return true;
    }

    
    public byte[] getDitheredB64EncodedPng() throws IOException {
        ByteArrayOutputStream byteArrayOutputStream = new ByteArrayOutputStream();
        DitheredImageProvider.getDitheredImage(this, byteArrayOutputStream);
        ByteArrayOutputStream byteArrayOutputStream2 = new ByteArrayOutputStream();
        int n = this.getWidth() / 8 + (this.getWidth() % 8 == 0 ? 0 : 1);
        BufferedImage bufferedImage = new BufferedImage(this.image.getWidth(), this.image.getHeight(), 12);
        this.removePixelPaddingFromRaster(this.getWidth(), this.getHeight(), byteArrayOutputStream, bufferedImage, n);
        ImageIO.write((RenderedImage)bufferedImage, "png", byteArrayOutputStream2);
        return Base64.encodeBytes(byteArrayOutputStream2.toByteArray(), 8).getBytes();
    }

    private function removePixelPaddingFromRaster($n, $n2, $byteArrayOutputStream, $bufferedImage, $n3)
    {
        $writableRaster = bufferedImage.getRaster();
        $arrby = byteArrayOutputStream.toByteArray();
        $by = 0;
        for ($i = 0; $i < $n2; ++$i) {
            for ($j = 0; $j < $n; ++$j) {
                $by = $arrby[$i * $n3 + $j / 8];
                $by = ($by & 1 << 7 - $j % 8) == 0 ? 0 : 1;
                writableRaster.setSample($j, $i, 0, $by);
            }
        }
    }
}