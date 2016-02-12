<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Posprint\Graphics\Zebra;

/**
 * Description of DitheredImageProvider
 *
 * @author administrador
 */
class DitheredImageProvider
{
    public static function getDitheredImage($img)
    {
        $width = imagesx($img);
        $heigth = imagesy($img);
        return self::getDitheredImage($width, $heigth, $img);
    }
    
    protected static function getDitheredImage($n, $n2, $zebraImageInternal)
    {
        $n3 = 0;
        //$arrn = zebraImageInternal.getRow(0);
        //$arrn2 = zebraImageInternal.getRow(1);
        $n4 = $n / 8 + ($n % 8 == 0 ? 0 : 1);
        $n5 = 8 - $n % 8;
        if ($n5 == 8) {
            $n5 = 0;
        }
        byte[] arrby = new byte[n4];
        int n6 = 0;
        for (n3 = 0; n3 < n; ++n3) {
            arrn[n3] = DitheredImageProvider.convertByteToGrayscale(arrn[n3]);
        }
        for (n3 = 0; n3 < n2; ++n3) {
            int n7;
            for (n7 = 0; n7 < arrby.length; ++n7) {
                arrby[n7] = 0;
            }
            n7 = 0;
            for (int i = 0; i < n; ++i) {
                if (i % 8 == 0) {
                    n6 = -128;
                }
                int n8 = arrn[i];
                n7 = i / 8;
                int n9 = n8 >= 128 ? -1 : 0;
                arrby[n7] = (byte)(arrby[n7] | n6 & n9);
                int n10 = n8 - (n9 & 255);
                if (i < n - 1) {
                    arrn[i + 1] = arrn[i + 1] + 7 * n10 / 16;
                }
                if (i > 0 && n3 < n2 - 1) {
                    arrn2[i - 1] = arrn2[i - 1] + 3 * n10 / 16;
                }
                if (n3 < n2 - 1) {
                    if (i == 0) {
                        arrn2[i] = DitheredImageProvider.convertByteToGrayscale(arrn2[i]);
                    }
                    arrn2[i] = arrn2[i] + 5 * n10 / 16;
                }
                if (n3 < n2 - 1 && i < n - 1) {
                    arrn2[i + 1] = DitheredImageProvider.convertByteToGrayscale(arrn2[i + 1]);
                    arrn2[i + 1] = arrn2[i + 1] + 1 * n10 / 16;
                }
                n6 = (byte)((n6 & 255) >>> 1);
            }
            arrby[n7] = (byte)(arrby[n7] | 255 >>> 8 - n5);
            outputStream.write(arrby);
            arrn = arrn2;
            arrn2 = zebraImageInternal.getRow(n3 + 2);
        }
    }
    
    public function getRow($n)
    {
        if ($n >= this.getHeight()) {
            return null;
        }
        int[] arrn = new int[this.getWidth()];
        this.image.getRGB(0, n, this.getWidth(), 1, arrn, 0, this.getWidth());
        return arrn;
    }
    
    
    public static function convertByteToGrayscale($n)
    {
        $n2 = self::unsrshift(($n & 16711680), 16);
        $n3 = self::unsrshift(($n & 65280), 8);
        $n4 = $n & 255;
        $n5 = ($n2 * 30 + $n3 * 59 + $n4 * 11) / 100;
        if ($n5 > 255) {
            $n5 = 255;
        } else if ($n5 < 0) {
            $n5 = 0;
        }
        return $n5;
    }
    
    private static function uRShift($a, $b)
    {
        if($b == 0) return $a;
        return ($a >> $b) & ~(1<<(8*PHP_INT_SIZE-1)>>($b-1));
    }
    
    private static function unsrshift($a, $b)
    {
        return ($a >= 0) ? ($a >> $b) : (($a & 0x7fffffff) >> $b) | (0x40000000 >> ($b - 1));
    }
}
