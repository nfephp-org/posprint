<?php

namespace Posprint\Connectors;

/**
 * Class Buffer
 * In principle, the entire assembly of RAW commands must be made for this buffer
 * that will be used later for sending to the appropriate connector set
 * by calling class
 * this is necessary to make to enable:
 *    1 - debug the assembly of commands
 *    2 - allow the use of qz.io for printing by using a browser and a cloud server
 *
 * @category  NFePHP
 * @package   Posprint
 * @author    Roberto L. Machado <linux.rlm@gmail.com>
 * @copyright 2016 Roberto L. Machado
 * @license   http://www.gnu.org/licenses/lesser.html LGPL v3
 * @link      http://github.com/nfephp-org/posprint
 *            for the canonical source repository
 */

use Posprint\Connectors\ConnectorInterface;

final class Buffer implements ConnectorInterface
{
    private $ctrlCodes = [
       ' [NUL] ' => 0, //Nulo
       ' [EOT] ' => 4, //EOT fim da transmissão
       ' [ENQ] ' => 5, //ENQ colocar na fila Pedido de status 1
       ' [BEL] ' => 7, //BEL sinal sonoro
       ' [HT] ' => 9, //tabulação horizontal
       ' [LF] ' => 10, //Inicia a impressão e avança uma linha
       ' [VT] ' => 11, //tabulação vertical
       ' [FF] ' => 12, //avança pagina
       ' [CR] ' => 13, //retorno de carro
       ' [SO] ' => 14, //SO Inicia modo expandido
       ' [SI] ' => 15, //Seleciona modo condensado
       ' [DLE] ' => 16, //Data Link Escape
       ' [DC1] ' => 17, //DC1 Inicia modo enfatizado
       ' [DC2] ' => 18, //DC2 Cancela modo condensado
       ' [DC3] ' => 19, //DC3 Cancela modo enfatizado
       ' [DC4] ' => 20, //DC4 Controle de dispositivo 4 Inicia modo normal
       ' [NAK] ' => 21, // NAK
       ' [SYN] ' => 22, //Sincronismo
       ' [CAN] ' => 24, //CAN Cancela linha enviada
       ' [EM] ' => 25, //Avança 4 linhas
       ' [ESC] ' => 27, //escape
       ' [FS] ' => 28, //FS
       ' [GS] ' => 29, //GS
       ' [DEL] ' => 127 //Cancela último caracter
    ];
    
    /**
     * Buffer of accumulated raw data.
     *
     * @var array
     */
    private $buffer = null;
    
    /**
     * Create new print connector
     * and set $buffer property as empty array
     */
    public function __construct()
    {
        $this->buffer = array();
    }

    /**
     * Destruct print connection
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Send data to buffer porperty
     *
     * @param string $data
     */
    public function write($data)
    {
        $this->buffer[] = $data;
    }
    
    /**
     * Read data form buffer
     *
     * @param  int $len
     * @return string
     */
    public function read($len = null)
    {
        return $this->getDataReadable(false);
    }

    /**
     * Finalize printer connection
     * and clear buffer property
     */
    public function close()
    {
        $this->buffer = array();
    }
    
    /**
     * Return the accumulated raw data that has been sent to this buffer.
     *
     * @param  bool $retArray Enable return as array, otherwise will return a string
     * @return string|array
     */
    public function getDataBinary($retArray = true)
    {
        if (! $retArray) {
            return implode($this->buffer);
        }
        return $this->buffer;
    }
    
    /**
     * Return the data buffer in base64-encoded for transmission over TCP/IP,
     * specifically for use qz.io or other similar system.
     *
     * @param  boolean $retArray Enable return as array, otherwise will return a string
     * @return array|string
     */
    public function getDataBase64($retArray = true)
    {
        $lbuff = $this->zConvArray('B');
        if (! $retArray) {
            return implode("\n", $lbuff);
        }
        return $lbuff;
    }
    
    /**
     * Returns the buffer data in JSON format
     * for use with the java ajax
     * It must be tested because there may be binary data
     * that can not travel on GET or POST requests over TCP/IP
     *
     * @param  bool $retArray Enable return as array, otherwise will return a string
     * @return string
     */
    public function getDataJson($retArray = true)
    {
        return json_encode($this->getDataBinary($retArray));
    }
    
    /**
     * getDataReadable
     * Return buffer data converted into a readable string.
     * For testing and debbuging only, this format should not be sent to printer
     *
     * @param  bool $retArray Enable return as array, otherwise will return a string
     * @return string|array
     */
    public function getDataReadable($retArray = true)
    {
        $ret = $this->zConvArray('R');
        if (! $retArray) {
            $ret = implode("\n", $ret);
        }
        return $ret;
    }
    
    /**
     * Convert buffer content
     *
     * @param string $type
     * @return array
     */
    protected function zConvArray($type)
    {
        $ret = array();
        foreach ($this->buffer as $data) {
            if ($type == 'R') {
                $ret[] = $this->friendlyBinary($data);
            }
            if ($type == 'B') {
                $ret[] = base64_encode($data);
            }
        }
        return $ret;
    }

    /**
     * Converts unprintable characters in screen-printing characters
     * used for debugging purpose only
     *
     * @param  string $input
     * @return string
     */
    protected function friendlyBinary($input)
    {
        // Print out binary data with PHP \x00 escape codes,
        // for builting test cases.
        $chars = str_split($input);
        foreach ($chars as $index => $byte) {
            $code = ord($byte);
            $key = array_search($code, $this->ctrlCodes, true);
            if ($key !== false) {
                $chars[$index] = $key;
            } elseif ($code < 32 || $code > 126) {
                $chars[$index] = " (" . bin2hex($byte) . "h) ";
            }
        }
        return implode($chars);
    }
}
