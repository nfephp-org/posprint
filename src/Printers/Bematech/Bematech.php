<?php

/**
 * Classe Bematech das impressoras POS.
 *
 * Foi construída em torno dos comandos da Bematech MP-4200 TH
 * Velocidade de impressão 250 mm/s
 * Resolução: 203 dpi
 * Largura de Impressão: Papel 80 mm (Máx. 72 mm) / Papel 58 mm (Máx. 54 mm)
 *
 *
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

include_once 'DefaultPrinter.php';

class Bematech extends DefaultPrinter
{
    public $mode = 'ESCPOS';

    public $charsetcode = 'ISO8859-1';

    private $model = '4200';

    protected $aCharSetTable = array(
        'ISO8859-1' => array('conv' => 'ISO8859-1', 'table' => '0', 'desc' => 'ISO8859-1: Latin'),
        'CP850' => array('conv' => '850', 'table' => '1', 'desc' => 'PC850: Multilingual'),
        'ABICOMP' => array('conv' => '', 'table' => '2', 'desc' => 'ABICOMP'),
        'CP437' => array('conv' => '437', 'table' => '3', 'desc' => 'CP437')
    );

    /**
     * List all avaiable fonts
     *
     * @var array
     */
    protected $aFont = array(0 => 'A', 1 => 'B');

    /**
     * setPrinterMode
     * Seta o modo de impressão no caso da Bematech seleciona entre o
     * padrão de comandos ESC/POS e alternativo ESC/BEMA
     * @param type $printerMode
     */
    public function setPrinterMode($printerMode = 'ESCPOS')
    {
        //padrão é ESC/POS
        $nmode = 1;
        if ($printerMode == 'ESCBEMA') {
            $this->printerMode = 'ESCBEMA';
            $nmode = 0; //???
        }
        $this->buffer->write(self::GS . chr(249) . chr(53) . chr($nmode));
    }

    public function setModel($model = '4200')
    {
        $this->model = $model;
    }

    /**
     * Set expanded mode.
     *
     * @param int $size qualquer valor ativa e null desativa
     */
    public function setExpanded($size = null)
    {
        $mode = array_keys($this->aFont, $this->font, true);
        if ($this->boldMode) {
            $mode[0] += 8;
        }
        if (!is_null($size)) {
            $mode = array_keys($this->aFont, $this->font, true);
            //double width and double height
            $mode[0] += (16 + 32);
        }
        $this->buffer->write(self::GS . '!' . 2);
    }

    /**
     * Set condensed mode.
     * Will change Font do D
     */
    public function setCondensed()
    {
        $this->setExpanded();
        $this->setFont('A');
    }

    /**
     * Print an image, using the older "bit image" command. This creates padding on the right of the image,
     * if its width is not divisible by 8.
     *
     * Should only be used if your printer does not support the graphics() command.
     *
     * @param Graphics $img The image to print
     * @param Graphics $size Size modifier for the image.
     */
    function bitImage($img, $size = 0)
    {
        $header = $this->dataHeader(array($img->getWidthBytes(), $img->getHeight()), true);
        if ($this->printerMode == "ESCPOS") {
            $this->buffer->write(self::GS . "v0" . chr($size) . $header);
            $this->buffer->write($img->getRasterImage());
        } else {
            $this->buffer->write(self::GS . "v0" . chr($size) . $header . $img->getRasterImage());
        }
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
        if ($this->printerMode == 'ESCPOS') {
            parent::text($text, $translate);
        } else {
            if ($translate) {
                $text = parent::zTranslate($text);
            }
            $this->buffer->write(self::ESC . chr(51) . chr(5));
            $this->buffer->write("$text\n");
        }
    }
}
