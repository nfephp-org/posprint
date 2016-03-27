<?php

namespace Posprint\Connectors;

/**
 * Class File
 * Create a binary file and writes the data line by line.
 * And it can also be used to provide direct connections to USB ports.
 *
 * @category  NFePHP
 * @package   Posprint
 * @copyright Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Connectors\ConnectorInterface;
use Posprint\Connectors\File;
use RuntimeException;
use InvalidArgumentException;

class Network extends File implements ConnectorInterface
{
    /**
     * Open a connection to a TCP/IP socket for ethernet printer connections
     *
     * @param  string $hostname
     * @param  int    $port
     * @throws RuntimeException
     */
    public function __construct($hostname = '', $port = 9100)
    {
        if (empty($hostname)) {
            throw new InvalidArgumentException("A hostname or a valid IP must be passed.");
        }
        if (empty($port) || ! is_numeric($port)) {
            $port = 9100;
        }
        $errno = 0;
        $errstr = '';
        $this->resource = @fsockopen($hostname, $port, $errno, $errstr);
        if ($this->resource === false) {
            throw new RuntimeException("Cannot initialise NetworkPrintConnector: " . $errstr);
        }
    }
}
