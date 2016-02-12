<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Posprint\Graphics\Zebra;

/**
 * Description of CompressedBitmapOutputStreamCpcl
 *
 * @author administrador
 */
class CompressedBitmapOutputStreamCpcl extends CompressedBitmapOutputStreamA
{
    public CompressedBitmapOutputStreamCpcl(OutputStream outputStream) {
        this.outputStream = outputStream;
        this.internalEncodedBuffer = new ByteArrayOutputStream();
    }

    @Override
    public void write(byte[] arrby) throws IOException {
        for (int i = 0; i < arrby.length; ++i) {
            this.bufferAndWrite((char)(~ arrby[i]));
        }
    }
}

