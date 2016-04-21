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
use RuntimeException;
use InvalidArgumentException;

class File implements ConnectorInterface
{
    /**
     * @var resource The file pointer to send data to.
     */
    protected $resource = false;

    /**
     * Construct new connector, given a filename
     * If created a binary file must be granted the necessary
     * permissions to create and write the system file
     *
     * @param string $filename
     */
    public function __construct($filename = null)
    {
        if (is_null($filename) || empty($filename)) {
            throw new InvalidArgumentException("A filepath must be passed!");
        }
        $command = 'rb+';
        if (! is_file($filename)) {
            $command = 'wb+';
        }
        $this->resource = @fopen($filename, $command);
        if ($this->resource === false) {
            throw new RuntimeException("Failed to open the file. Check the permissions!");
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
        if (is_resource($this->resource)) {
            if (! @fclose($this->resource)) {
                //when a fclose returns false ??
            }
        }
        $this->resource = false;
    }

    /**
     * Write data to the file
     *
     * @param string $data
     * @return int
     */
    public function write($data = '')
    {
        if (is_resource($this->resource) && !empty($data)) {
            return (int) fwrite($this->resource, $data);
        }
        return 0;
    }
    /**
     * Read some bytes from file
     *
     * @param  int $len
     * @return stirng
     */
    public function read($len = null)
    {
        $data = '';
        if (!is_null($len) && is_numeric($len)) {
            $len = ceil($len);
            return fread($this->resource, $len);
        }
        while (!feof($this->resource)) {
            $data .= fread($this->resource, 4096);
        }
        return $data;
    }
}
