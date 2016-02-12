<?php

namespace Posprint\Graphics\Zebra;

abstract class CompressedBitmapOutputStreamA extends OutputStream
{
    private static final int INTERNAL_ENCODED_BUFFER_SIZE = 1024;
    protected OutputStream outputStream;
    protected ByteArrayOutputStream internalEncodedBuffer;

    @Override
    public void write(int n) throws IOException {
        throw new IOException("This method is not implemented.");
    }

    @Override
    public void close() throws IOException {
        this.flush();
    }

    @Override
    public void flush() throws IOException {
        if (this.internalEncodedBuffer.size() != 0) {
            this.outputStream.write(this.internalEncodedBuffer.toByteArray());
            this.internalEncodedBuffer.reset();
        }
    }

    protected void bufferAndWrite(char c) throws IOException {
        if (this.internalEncodedBuffer.size() < 1024) {
            this.internalEncodedBuffer.write((byte)c);
        }
        if (this.internalEncodedBuffer.size() == 1024) {
            this.outputStream.write(this.internalEncodedBuffer.toByteArray());
            this.internalEncodedBuffer.reset();
        }
    }
}
