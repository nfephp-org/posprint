<?php

include_once 'Bematech.php';

class BematechMP20MI extends Bematech
{

    public function bitImage($img, $size = 0)
    {
        // parent...
    }

    public function setJustification()
    {
        // parent...
    }

    /**
     * Send message or command to buffer
     * when sending commands is not required to convert characters,
     * so the variable may translate by false
     *
     * @param string $text
     * @param bool $translate
     */
    public function text($text = '', $translate = true)
    {
        if ($translate) {
            $text = parent::zTranslate($text);
        }
        $this->buffer->write($text);
    }

    public function lineFeed($lines = 1)
    {
        for ($line = 1; $line <= $lines; $line++) {
            $this->buffer->write(self::LF . chr(10) . chr(13));
            parent::lineFeed(1);
        }
    }
}
