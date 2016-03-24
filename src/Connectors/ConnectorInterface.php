<?php

namespace Posprint\Connectors;

/**
 * Classe Connector interface
 *
 * @category  NFePHP
 * @package   Posprint
 * @copyright Copyright (c) 2015
 * @license   http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/posprint for the canonical source repository
 */

interface ConnectorInterface
{
    /**
     * Print connectors close
     */
    public function __destruct();

    /**
     * Finish using this print connector (close file, socket, send
     * accumulated output, etc).
     */
    public function close();

    /**
     * Send data to printer sends through the connector
     *
     * @param string $data
     */
    public function write($data);
    
    /**
     * Read data from connector
     */
    public function read($len);
}
