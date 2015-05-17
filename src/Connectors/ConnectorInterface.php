<?php

namespace Posprint\Connectors;

interface Connector
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
     * @param string $data
     */
    public function write($data);
}
