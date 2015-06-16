<?php

namespace Posprint\Connectors;

use Posprint\Connectors\Connector;
use Exception;

class File implements Connector
{
    /**
     * @var resource The file pointer to send data to.
     */
    protected $resource = false;

    /**
     * Construct new connector, given a filename
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
