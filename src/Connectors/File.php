<?php

namespace Posprint\Connectors;

use Posprint\Connectors;

class File implements Connector
{
    /**
     * @var resource The file pointer to send data to.
     */
    protected $filePointer = false;

    /**
     * Construct new connector, given a filename
     * 
     * @param string $filename
     */
    public function __construct($filename)
    {
        if (empty($filename)) {
            return false;
        }
        $this->filePointer = fopen($filename, "wb+");
        if ($this->filePointer === false) {
            throw new Exception("Impossivel abrir o arquivo. Permissões!.");
        }
    }

    public function __destruct()
    {
        if ($this->filePointer != false) {
            $this->close();
        }
    }

    public function getFilepointer()
    {
        return $this->filePointer;
    }
    
    /**
     * Close file pointer
     */
    public function close()
    {
        fclose($this->filePointer);
        $this->filePointer = false;
    }

    /**
     * Write data to the file
     * 
     * @param string $data
     */
    public function write($data)
    {
        fwrite($this->filePointer, $data);
    }
}
