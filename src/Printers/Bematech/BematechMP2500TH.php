<?php

include_once 'Bematech.php';

class BematechMP2500TH extends Bematech
{

    /**
     * Cut the paper
     * @param int $mode CUT_FULL or CUT_PARTIAL. If not specified, CUT_FULL will be used.
     * @param int $lines Number of lines to feed after cut
     *   If m = 0 or m = 48 perform a full paper cut
     *   If m = 1 or m = 49 perform a partial paper cut.
     */
    public function cut($mode = 65, $lines = 3)
    {
        if ($lines > 0) {
            for ($line = 1; $line <= $lines; $line++) {
                $this->buffer->write(self::LF . chr(10) . chr(13));
                parent::lineFeed(1);
            }
        }
        $this->buffer->write(self::GS . 'V' . chr(27) . chr(109));
    }

    public function lineFeed($lines = 1)
    {
        for ($line = 1; $line <= $lines; $line++) {
            $this->buffer->write(self::LF . chr(10) . chr(13));
            parent::lineFeed(1);
        }
    }
}
