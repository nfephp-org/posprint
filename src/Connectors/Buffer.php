<?php

namespace Posprint\Connectors;

/**
 * Trait Buffer
 * In principle, the entire assembly of RAW commands must be made for this buffer
 * That will be used later for sending to the appropriate connector set by calling class
 * This is necessary to make to enable:
 *    1 - debug the assembly of commands
 *    2 - allow the use of qz.io for printing by using a browser and a cloud server
 *
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Connectors\ConnectorInterface;

trait Buffer implements ConnectorInterface
{
    /**
     * Buffer of accumulated raw data.
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
     * send data to printer
     * @param string $data
     */
    public function write($data)
    {
        $this->buffer[] = $data;
    }

    /**
     * Finalize printer connection
     */
    public function close()
    {
        $this->buffer = null;
    }

    /**
     * Get the accumulated raw data that has been sent to this buffer.
     * @param bool $retArray Enable return as array, otherwise will return a string
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
     * Returns the buffer data in JSON format
     * for use with the java applet
     * @param bool $retArray
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
     * @param bool $retArray Enable return as array, otherwise will return a string
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
     * friendlyBinary
     * Converts unprintable characters in screen-printing characters
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
