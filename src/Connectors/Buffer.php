<?php

namespace Posprint\Connectors;

use Posprint\Connectors;

class Buffer implements Connector
{
    /**
     * Buffer of accumulated data.
     * @var array
     */
    private $buffer = null;
    
    /**
     * Create new print connector
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
     * Finalize printer connection
     */
    public function close()
    {
        $this->buffer = null;
    }

    /**
     * Get the accumulated data that has been sent to this buffer.
     * @param bool $retArray
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
     * getDataJson
     * Retorna os dados do buffer em formato json
     * para uso junto com o applet jZebra
     * @param bool $retArray
     * @return string
     */
    public function getDataJson($retArray = true)
    {
        return json_encode($this->getDataBinary($retArray));
    }
    
    /**
     * getDataReadable
     * Retorna os dados do buffer convertido em string que podem
     * ser visualizadas. Pra efeito de testes, sem a impressora.
     * @param bool $retArray
     * @return string|array
     */
    public function getDataReadable($retArray = true)
    {
        if ($retArray) {
            $ret = array();
            foreach ($this->buffer as $data) {
                $ret[] = $this->friendlyBinary($data);
            }
        } else {
            $ret = $this->friendlyBinary(implode($this->buffer));
        }
        return $ret;
    }

    /**
     * send data to printer
     * @param string $data
     */
    public function write($data)
    {
        $this->buffer[] = $data;
    }
    
    /**
     * friendlyBinary
     * Converte os bytes não imprimiveis em tela para um formato 
     * legível
     * @param string $input
     * @return string
     */
    public function friendlyBinary($input)
    {
        // Print out binary data with PHP \x00 escape codes,
        // for builting test cases.
        $chars = str_split($input);
        foreach ($chars as $index => $byte) {
            $code = ord($byte);
            if ($code < 32 || $code > 126) {
                $chars[$index] = "\\x" . bin2hex($byte);
            }
        }
        return implode($chars);
    }
}
