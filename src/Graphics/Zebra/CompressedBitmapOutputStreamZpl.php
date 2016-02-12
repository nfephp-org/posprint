<?php

namespace Posprint\Graphics\Zebra;

use Posprint\Graphics\Zebra\CompressedBitmapOutputStreamA;

class CompressedBitmapOutputStreamZpl extends CompressedBitmapOutputStreamA
{
    private $previousByteWritten = 0;
    private $previousByteWrittenRepeatCount = 0;
    private static $charMap = [380, 360, 340, 320, 300, 280, 260, 240, 220, 200, 180, 160, 140, 120, 100, 80, 60, 40, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1];
    private static $charVal = ['y', 'x', 'w', 'v', 'u', 't', 's', 'r', 'q', 'p', 'o', 'n', 'm', 'l', 'k', 'j', 'i', 'h', 'g', 'Y', 'X', 'W', 'V', 'U', 'T', 'S', 'R', 'Q', 'P', 'O', 'N', 'M', 'L', 'K', 'J', 'I', 'H', 'G'];

    public function compressedBitmapOutputStreamZpl($outputStream)
    {
        this.outputStream = outputStream;
        this.internalEncodedBuffer = new ByteArrayOutputStream();
    }

    
    public function write($arrby)
    {
        for ($i = 0; $i < $arrby.length; ++$i) {
            $arrby2 = this.extractNibblesFromByte($arrby[$i]);
            this.writeNibblesToStream($arrby2);
        }
    }
    
    public function flush()
    {
        if (this.previousByteWrittenRepeatCount > 0) {
            this.sendBufferedDataToPrinter();
            this.previousByteWrittenRepeatCount = 0;
        }
        super.flush();
    }

    private function writeNibblesToStream($arrby)
    {
        for ($i = 0; $i < arrby.length; ++$i) {
            this.writeNibbleToStream($arrby[$i]);
        }
    }

    private function writeNibbleToStream($by)
    {
        if (this.previousByteWrittenRepeatCount == 0) {
            this.previousByteWritten = $by;
            ++this.previousByteWrittenRepeatCount;
        } else if (this.previousByteWritten == by) {
            ++this.previousByteWrittenRepeatCount;
        } else {
            this.sendBufferedDataToPrinter();
            this.previousByteWritten = by;
            this.previousByteWrittenRepeatCount = 1;
        }
    }

    private function sendBufferedDataToPrinter()
    {
        try {
            this.computeAndOutput();
        }
        catch (ConnectionException var1_1) {
            throw new IOException(var1_1.getMessage());
        }
    }

    private function computeAndOutput()
    {
        if (this.previousByteWrittenRepeatCount > 1) {
            int n;
            int n2 = this.previousByteWrittenRepeatCount / 400;
            int n3 = this.previousByteWrittenRepeatCount % 400;
            for (n = 0; n < n2; ++n) {
                this.bufferAndWrite('z');
            }
            for (n = 0; n < charMap.length; ++n) {
                if (n3 < charMap[n]) continue;
                this.bufferAndWrite(charVal[n]);
                n3 -= charMap[n];
            }
        }
        this.bufferAndWrite(Integer.toHexString(this.previousByteWritten & 15).toUpperCase().charAt(0));
    }

    private function extractNibblesFromByte($by)
    {
        $arrby = new byte[]{(byte)(~ by >> 4 & 15), (byte)(~ by & 15)};
        return $arrby;
    }
}