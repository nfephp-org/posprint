<?php

namespace Posprint\Connectors;

/**
 * Classe File
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

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
