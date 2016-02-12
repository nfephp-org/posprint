<?php

namespace Posprint\Connectors;

/**
 * Classe Network
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

class Network implements ConnectorInterface
{
    private $resource = false;
    private $ipaddress = '';
    private $port = 9100;
    private $timeout = 30;
            
    public function __construct($ipaddress = '', $port = 9100, $timeout = 30)
    {
        $this->setIpaddress($ipaddress);
        $this->setPort($port);
        $this->setTimeout($timeout);
        $this->initialize();
    }
    
    public function __destruct()
    {
        $this->close();
    }
    
    public function setIpaddress($ipaddress = '')
    {
        if ($ipaddress != '') {
            $this->ipaddress = $ipaddress;
        }
    }
    
    public function getIpaddress()
    {
        return $this->ipaddress;
    }
    
    public function setPort($port = 9100)
    {
        if ($port != '') {
            $this->port = $port;
        }
    }
    
    public function getPort()
    {
        return $this->port;
    }
    
    public function setTimeout($timeout = 30)
    {
        if ($timeout != '' && is_numeric($timeout)) {
            $this->timeout = $timeout;
        }
    }
    
    public function getTimeout()
    {
        return $this->timeout;
    }
    
    protected function initialize()
    {
        if ($this->ipaddress == '') {
            return;
        }
        $this->open();
    }
    
    public function open($ipaddress = '', $port = '', $timeout = '')
    {
        $errorNo = '';
        $errorMsg = '';
        $this->setIpaddress($ipaddress);
        $this->setPort($port);
        $this->setTimeout($timeout);
        $this->resource = fsockopen(
            $this->ipaddress,
            $this->port,
            $errorNo,
            $errorMsg,
            $this->timeout
        );
        if ($this->resource === false) {
            $msg = "Não foi possivel a conexão com [$this->ipaddress : $this->port] - $errorMsg.";
            throw new Exception($msg);
        }
    }

    public function close()
    {
        fclose($this->resource);
    }
    
    public function write($data)
    {
        fwrite($this->resource, $data);
    }
}
