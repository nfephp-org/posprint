<?php

namespace Posprint\Connectors;

/**
 * Trait File
 * Create a binary file and writes the data line by line.
 * And it can also be used to provide direct connections to USB ports.
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Connectors\ConnectorInterface;
use Exception;

class File implements ConnectorInterface
{
    /**
     * @var resource The file pointer to send data to.
     */
    protected $resource = false;

    /**
     * Construct new connector, given a filename
     * If created a binary file must be granted the necessary permissions to create and write the system file
     * @param string $filename
     */
    public function __construct($filename)
    {
        if (empty($filename)) {
            return false;
        }
        $this->resource = fopen($filename, "wb+");
        if ($this->resource === false) {
            throw new Exception("Impossivel abrir o arquivo. Permissões!.");
        }
    }

    /**
     * Destruct conection closing the file
     */
    public function __destruct()
    {
        if ($this->resource != false) {
            $this->close();
        }
    }
      
    /**
     * Close file pointer
     */
    public function close()
    {
        fclose($this->resource);
        $this->resource = false;
    }

    /**
     * Write data to the file
     * @param string $data
     */
    public function write($data)
    {
        fwrite($this->resource, $data);
    }
}
