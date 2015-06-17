<?php

namespace Posprint\Connectors;

/**
 * Classe Serial
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Connectors;
use PhpSerial;

class Serial implements Connector
{
    protected $resource;
    protected $device = "/dev/ttyS0";
    protected $baudRate = 9600;
    protected $byteSize = 8;
    protected $parity = 'none';
    protected $deviceStatus = null;
    
    public function __construct($device = "/dev/ttyS0", $baudRate = 9600, $byteSize = 8, $parity = 'none')
    {
        $this->setDevice($device);
        $this->setBaudRate($baudRate);
        $this->setByteSize($byteSize);
        $this->setParity($parity);
        $this->initialize();
    }
    
    protected function initialize()
    {
        $this->resource = new PhpSerial();
        $this->resource->deviceSet($this->device);
        $this->resource->confBaudRate($this->baudRate);
        $this->resource->confCharacterLength($this->byteSize);
        $this->resource->confParity($this->parity);
        $this->deviceStatus = $this->resource->deviceOpen();
    }
    
    public function __destruct()
    {
        if ($this->deviceStatus == true) {
            $this->close();
        }
    }
    
    public function setDevice($device = "/dev/ttyS0")
    {
        $this->device = $device;
    }
    
    public function setBaudRate($baudRate = 9600)
    {
        $this->baudRate = $baudRate;
    }
    
    public function setByteSize($byteSize = 8)
    {
        $this->byteSize = $byteSize;
    }
    
    public function setParity($parity = 'none')
    {
        $this->parity = $parity;
    }
    
    /**
     * Finalize printer connection
     */
    public function close()
    {
        $this->deviceStatus = ! $this->resource->deviceClose();
    }

    /**
     * send data to printer
     * @param string $data
     */
    public function write($data)
    {
        $this->resource->sendMessage($data);
    }
}
