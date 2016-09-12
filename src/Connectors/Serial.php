<?php

namespace Posprint\Connectors;

/**
 * Class Serial
 *
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Connectors\ConnectorInterface;
use Posprint\Extras\PhpSerial;

class Serial implements ConnectorInterface
{
    /**
     *
     * @var PhpSerial
     */
    protected $resource;
    /**
     *
     * @var bool
     */
    protected $deviceStatus = false;
    
    /**
     * Construct
     * Set and open serial device
     * @param string $device
     * @param int $baudRate
     * @param int $byteSize
     * @param string $parity
     * @param string $stopbits
     * @param string $flowctrl
     */
    public function __construct(
        $device = "/dev/ttyS0",
        $baudRate = 9600,
        $byteSize = 8,
        $parity = 'none',
        $stopbits = '1',
        $flowctrl = 'none'
    ) {
        $this->resource = new PhpSerial();
        $this->resource->setPort($device);
        $this->resource->setBaudRate($baudRate);
        $this->resource->setDataBits($byteSize);
        $this->resource->setParity($parity);
        $this->resource->setFlowControl($flowctrl);
        $this->resource->setStopBits($stopbits);
        $this->resource->setUp();
        $this->deviceStatus = $this->resource->open();
    }
    
    /**
     * End class
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
        $this->deviceStatus = ! $this->resource->close();
    }

    /**
     * send data to printer
     * @param string $data
     */
    public function write($data)
    {
        $this->resource->write($data);
        $this->resource->flush();
    }
    
    /**
     * Read serial port
     * @param int $len
     * @return string
     */
    public function read($len = 0)
    {
        return $this->resource->read($len);
    }
}
