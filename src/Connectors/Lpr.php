<?php

namespace Posprint\Connectors;

/**
 * Classe Lpr
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

class Lpr implements ConnectorInterface
{
    /**
     *
     * @var resource
     */
    private $resource = false;
    
    /**
     * Printer's host.
     * Initialize by constructor
     * @var     string
     */
    protected $host = '';
    
    protected $printerName = '';
    
    /**
     * Printer's Port. 
     * Default port 515 (see constructor),
     * but it can change with the function setPort
     * @var     integer
     */
    protected $port = 515;
    
    /**
     * Max seconds to connect to the printer.
     * Default 20, but it can change with the function setTimeOut
     * @var     integer
     */
    protected $timeout = 30;
    
    /**
     * Username for printer
     * @var     string
     */
    protected $username = 'LPR';
    protected $password = '';
    
    
    /**
     * Debug message
     * @var     array
     */
    protected $printDebug = array();
    
    public function __construct($host = '', $printer = '', $user = '', $pass = '', $port = 515)
    {
        $this->setHost($host);
        $this->setPort($port);
        $this->setUser($user);
        $this->setPassword($pass);
        $this->initialize();
        
    }
    
    public function __destruct()
    {
        $this->close();
    }
    
    protected function initialize()
    {
        if ($this->host == '') {
            return;
        }
        $this->open();
    }
    
    public function setHost($host = '')
    {
        if ($host != '') {
            $this->host = $host;
        }
    }
    
    public function getHost()
    {
        return $this->host;
    }

    public function setPrinter($printer = '')
    {
        if ($printer != '') {
            $this->printerName = $printer;
        }
    }
    
    public function getPrinter()
    {
        return $this->printerName;
    }

    
    public function setUser($user = '')
    {
        if ($user != '') {
            $this->username = $user;
        }
    }
    
    public function getUser()
    {
        return $this->username;
    }

    public function setPassword($pass = '')
    {
        if ($pass != '') {
            $this->password = $pass;
        }
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    
    public function setPort($port = 515)
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
   
    /**
     * Connect to printer
     * @return    socket    Connection
     */
    private function open()
    {
        $errorNumber = '';
        $errorMsg = '';
        $this->resource = stream_socket_client(
            "tcp://".$this->host.":".$this->port,
            $errorNumber,
            $errorMsg,
            $this->timeout
        );
        if ($this->resource === false) {
            $msg = "Não foi possivel a conexão com [$this->host : $this->port] - $errorMsg.";
            throw new Exception($msg);
        }
    }
   
    /**
     * Makes de cfA (control string)
     * @return    string    cfA control String
     */
    private function makeCfA($jobid)
    {
        $param = $this->username;
        if ($this->password != '') {
            $param = $this->username . '@' . $this->password;
        }
        $hostname = $_SERVER['REMOTE_ADDR'];
        $cfa  = "";
        $cfa .= "H" . $hostname . "\n"; //hostname
        $cfa .= "U" . $param . "\n"; //user
        $cfa .= "P" . $this->printerName . "\n"; //printer
        $cfa .= "fdfA" + $jobid + $hostname + "\n";
        return $cfa;
    }
  
    /**
     * Print any waiting jobs
     * @return    boolean    cfA control String
     */
    public function printWaitingJobs($queue)
    {
        //Connecting to the network printer
        $connection = $this->open();
        if (!$connection) {
            $this->setError("Error in connection. Please change HOST or PORT.");
            return false;
        }
        //Print any waiting job
        fwrite($connection, chr(1).$queue."\n");
        $this->setMessage("Print any waiting job...");
        //Checking errors
        if (ord(fread($connection, 1)) != 0) {
            $this->setError("Error while start print jobs on queue " . $queue);
            //Close connection
            $this->close();
            return false;
        }
        //Close connection
        $this->close();
        return true;
    }

    public function close()
    {
        fclose($this->resource);
    }
    
    /**
     * Print a text message on network lpr printer
     * @param    string     $text     The name of the property
     * @return    boolean    True if success
     */
    public function write($text = '')
    {
        $queue = "defaultQueue";
        $jobid = 001;
        //Print any waiting job
        //$this->printWaitingJobs($queue);
        //Connecting to the network printer
        $connection = $this->connect();
        if (!$connection) {
            $this->setError("Error in connection. Please change HOST or PORT.");
            return false;
        }
        //Starting printer
        fwrite($connection, chr(2).$queue."\n");
        $this->setMessage("Starting printer...");
        //Checking errors
        if (ord(fread($connection, 1)) != 0) {
            $this->setError("Error while start printing on queue");
            fclose($connection);
            return false;
        }
        //Write control file    
        $ctrl = $this->makecfA($jobid);
        fwrite($connection, chr(2).strlen($ctrl)." cfA".$jobid.$this->username."\n");
        $this->setMessage("Sending control file...");
        //Checking errors
        if (ord(fread($connection, 1)) != 0) {
            $this->setError("Error while start sending control file");
            //Close connection
            fclose($connection);
            return false;
        }
        fwrite($connection, $ctrl.chr(0));
        //Checking errors
        if (ord(fread($connection, 1)) != 0) {
            $this->setError("Error while sending control file");
            //Close connection
            fclose($connection);
            return false;
        }
        //Send data string
        fwrite($connection, chr(3).strlen($text)." dfA".$jobid."\n");   
        $this->setMessage("Sending data...");
        //Checking errors
        if (ord(fread($connection, 1)) != 0) {
            $this->setError("Error while sending control file");
            //Close connection
            fclose($connection);
            return false;
        }
        fwrite($connection, $text.chr(0));
        //Checking errors
        if (ord(fread($connection, 1)) != 0) {
            $this->setError("Error while sending control file");
            //Close connection
            $this->close();
            return false;
        } else {
            $this->setMessage("Data received!!!");
        }
        //Close connection
        $this->close();
        return true;
    }
}
