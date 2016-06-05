<?php

namespace Posprint\Extras;

/**
 * Serial port control class
 *
 * Refactoring from original, https://github.com/Xowap/PHP-Serial, to meet PSR standards
 * and propose improvements and fixes to the fact that the original is not actively
 * maintained for many years.
 * by Roberto L. Machado <linux dot rlm at gmail dot com>
 *
 * IMPORTANT: check and adjust permissions for serial port access by server user like www-data
 *
 * @author    Rémy Sanchez <remy.sanchez@hyperthese.net>
 * @author    Rizwan Kassim <rizwank@geekymedia.com>
 * @thanks    Aurélien Derouineau for finding how to open serial ports with windows
 * @thanks    Alec Avedisyan for help and testing with reading
 * @thanks    Jim Wright for OSX cleanup/fixes.
 * @copyright under GPL 2 licence
 */

use RuntimeException;

class PhpSerial
{
    const OS_UNKNOWN = 0;
    const OS_WIN = 1; //WINS32 WINNT Windows
    const OS_LINUX = 2;
    const OS_CYGWIN = 3; //Cygwin Windows Linux like commands
    const OS_UNIX = 4;
    const OS_BSD = 5; //FreeBSD or NetBSD or OpenBSD /dev/ttyu1
    const OS_OSX = 6; //Darwin MacOS
    const OS_HPUX = 7; //tty1p0
    
    const SERIAL_DEVICE_NOTSET = 0;
    const SERIAL_DEVICE_SET = 1;
    const SERIAL_DEVICE_OPENED = 2;
    
    const PARITY_NONE = 0;
    const PARITY_ODD = 1;
    const PARITY_EVEN = 2;
    
    const FLOW_NONE = 0; //no flow control
    const FLOW_RTSCTS = 1; // use RTS/CTS handshaking
    const FLOW_XONXOFF = 2; //use XON/XOFF protocol
    
    /**
     * Pointer for device
     *
     * @var resource
     */
    protected $handle = null;
    /**
     * Data buffer
     *
     * @var string
     */
    protected $buffer = "";
    /**
     * This var says if buffer should be flushed by write (true) or
     * manually (false)
     *
     * @var bool
     */
    protected $autoflush = false;
    /**
     * Wait time after send data to serial
     *
     * @var float
     */
    protected $waittime = 0.1;
    /**
     * OS type where php is running
     * linux is default
     *
     * @var int
     */
    protected $ostype = 2;
    /**
     * Mode command to set up serial port
     * formated device mode for especific OS use
     *
     * @var string
     */
    protected $mode = '';
    /**
     * Status of port
     * NoSet, Set or Open
     *
     * @var int
     */
    protected $state = self::SERIAL_DEVICE_NOTSET;
    /**
     * Port name
     *
     * @var string
     */
    protected $port = '/dev/ttyS0';
    /**
     * Data bits
     *
     * @var int
     */
    protected $databits = 8;
    /**
     * Baud Rate
     *
     * @var int
     */
    protected $baudrate = 9600;
    /**
     * Parity
     *
     * @var int
     */
    protected $parity = self::PARITY_NONE;
    /**
     * Stop Bits
     *
     * @var float
     */
    protected $stopbits = 1;
    /**
     * Flow Control
     *
     * @var int
     */
    protected $flowcontrol = self::FLOW_NONE;
    /**
     * Formated device name command
     *
     * @var string
     */
    protected $device = '/dev/ttyS0';
    /**
     * Formated Data Bits command
     *
     * @var string
     */
    protected $formatedDataBits = 'cs8';
    /**
     * Formated Baud Rate command
     *
     * @var string
     */
    protected $formatedBaudRate = '9600';
    /**
     * Formated parity command
     *
     * @var string
     */
    protected $formatedParity = '-parenb';
    /**
     * Formated stop bits command
     *
     * @var string
     */
    protected $formatedStopBits = '-cstopb';
    /**
     * Formated flow control command
     *
     * @var string
     */
    protected $formatedFlowControl = 'clocal -crtscts -ixon -ixoff';
    
    /**
     * Parity data
     *
     * @var array
     */
    private $parityargs = [
        "none" => [0, "-parenb"],
        "odd"  => [1, "parenb parodd"],
        "even" => [2, "parenb -parodd"]
    ];
    
    /**
     * Basud Rate data
     *
     * @var array
     */
    private $baudsargs = array (
        110    => 11,
        150    => 15,
        300    => 30,
        600    => 60,
        1200   => 12,
        2400   => 24,
        4800   => 48,
        9600   => 96,
        19200  => 19,
        38400  => 38400,
        57600  => 57600,
        115200 => 115200
    );

    /**
     * Constructor
     * Set ostype parameter
     *
     * @param int $forceOS
     */
    public function __construct($forceOS = null)
    {
        if (! is_null($forceOS)) {
            if ($this->ostype !== $forceOS && ($forceOS > 0 && $forceOS < 8)) {
                $this->ostype = $forceOS;
                //clear params
                $this->clearParams();
            }
        } else {
            $this->ostype = $this->getOs();
        }
    }
    
    /**
     * Clear class params
     * Used for testing proporses
     */
    protected function clearParams()
    {
        $this->mode = null;
        $this->state = null;
        $this->port = null;
        $this->databits = null;
        $this->baudrate = null;
        $this->parity = null;
        $this->stopbits = null;
        $this->flowcontrol = null;
        $this->device = null;
        $this->formatedDataBits = null;
        $this->formatedBaudRate = null;
        $this->formatedParity = null;
        $this->formatedStopBits = null;
        $this->formatedFlowControl = null;
    }

    /**
     * Close port
     */
    public function __destruct()
    {
        $this->close();
    }
    
    /**
     * Open set port
     *
     * @return boolean
     */
    public function open()
    {
        if ($this->state === self::SERIAL_DEVICE_OPENED && is_resource($this->handle)) {
            return true;
        }
        $timeout = 10; //seconds
        for ($i = 0; $i < $timeout; $i++) {
            $this->handle = @fopen($this->device, 'r+bn');
            if ($this->handle) {
                break;
            }
            sleep(1);
        }
        if ($this->handle === false) {
            $this->handle = null;
            $this->state = self::SERIAL_DEVICE_NOTSET;
            throw new RuntimeException('Fail to open device. Check permissions.');
        }
        stream_set_blocking($this->handle, false);
        $this->state = self::SERIAL_DEVICE_OPENED;
        return true;
    }
    
    /**
     * Close serial port
     *
     * @return boolean
     */
    public function close()
    {
        if ($this->state !== self::SERIAL_DEVICE_OPENED || ! is_resource($this->handle)) {
            return true;
        }
        if (fclose($this->handle)) {
            $this->handle = null;
            $this->state = self::SERIAL_DEVICE_SET;
            return true;
        }
        return false;
    }
    
    /**
     * Returns the setup configuration for serial port
     * this command will be exectuted in terminal
     *
     * @return string
     */
    public function getSetUp()
    {
        return $this->mode;
    }
    
    /**
     * Use class parameters to configure the serial port
     * before the serial port is opened it must be configured,
     * and in windows environment, all sets at a single time
     *
     * @return bool
     */
    public function setUp()
    {
        if ($this->state === self::SERIAL_DEVICE_SET) {
            return true;
        }
        if ($this->ostype == 0) {
            return false;
        }
        $modesos = [
            1 => 'MODE', //windows mode com4: BAUD=9600 PARITY=n DATA=8 STOP=1 to=off dtr=off rts=off
            2 => "stty -F", //linux
            3 => "stty -F", //cygwin
            4 => 'stty -F', //unix
            5 => 'stty -F', //BSD
            6 => "stty -f", //MacOS
            7 => 'stty -F' //HPUX
        ];
        $mode = $modesos[$this->ostype]
                . " "
                . "$this->device "
                . "$this->formatedBaudRate "
                . "$this->formatedParity "
                . "$this->formatedDataBits "
                . "$this->formatedStopBits "
                . "$this->formatedFlowControl";
        
        $out = '';
        if ($this->execCommand($mode, $out) != 0) {
            throw new RuntimeException("SetUP fail with: ".$out[1]);
        }
        $this->mode = $mode;
        $this->state = self::SERIAL_DEVICE_SET;
        return true;
    }
    
    /**
     * Set automatic send massage to serial
     *
     * @param bool  $auto
     * @param float $waittime
     */
    public function setAuto($auto, $waittime)
    {
        if (! is_bool($auto)) {
            $data = false;
        }
        if (! is_float($waittime)) {
            $waittime = 0.1;
        }
        $this->waittime = $waittime;
        $this->autoflush = $auto;
    }
    
    /**
     * Returns automatic mode
     *
     * @return bool
     */
    public function getAuto()
    {
        return $this->autoflush;
    }

    /**
     * Read serial port
     *
     * @param  int $count Number of characters to be read (will stop before
     *                   if less characters are in the buffer)
     * @return string
     */
    public function read($count = 0)
    {
        if ($this->state !== self::SERIAL_DEVICE_OPENED) {
            return '';
        }
        $content = "";
        $i = 0;
        // Windows port reading procedures still buggy
        // Behavior in OSX isn't to wait for new data to recover, but just
        // grabs what's there! Doesn't always work perfectly for me in OSX
        if ($count !== 0) {
            do {
                if ($i > $count) {
                    $content .= fread($this->handle, ($count - $i));
                } else {
                    $content .= fread($this->handle, 128);
                }
            } while (($i += 128) === strlen($content));
            return $content;
        }
        do {
            $content .= fread($this->handle, 128);
        } while (($i += 128) === strlen($content));
        return $content;
    }

    /**
     * Write data to buffer or serial port
     * depends of getAuto()
     * if  getAuto() == true this command writes directly to port
     * if  getAuto() == false this command writes to buffer (default)
     *
     * @param  string $data
     * @return boolean
     */
    public function write($data)
    {
        if ($this->state !== self::SERIAL_DEVICE_OPENED) {
            return '';
        }
        $this->buffer .= $data;
        if ($this->autoflush === true) {
            $this->flush();
            usleep((int) ($this->waittime * 1000000));
        }
        return true;
    }
    
    /**
     * Flushs imediatly data to serial port
     *
     * @return boolean
     */
    public function flush()
    {
        if ($this->state !== self::SERIAL_DEVICE_OPENED) {
            return '';
        }
        if (fwrite($this->handle, $this->buffer) !== false) {
            $this->buffer = "";
            return true;
        }
        return false;
    }

    /**
     * Set port name
     *
     * @param string $port
     */
    public function setPort($port)
    {
        //identify input if $port like COM?? even in others OS
        $flagWinMode = preg_match("@^COM(\d+):?$@i", $port, $matches);
        //select port from OS type
        switch ($this->ostype) {
            case self::OS_WIN:
                $this->device = ($flagWinMode) ? "COM$matches[1]:" : $port;
                break;
            case self::OS_LINUX:
            case self::OS_CYGWIN:
                $this->device = ($flagWinMode) ? "/dev/ttyS".($matches[1]-1) : $port;
                break;
            case self::OS_UNIX:
            case self::OS_BSD:
            case self::OS_OSX:
            case self::OS_HPUX:
            default:
                $this->device = $port;
        }
        $this->port = $port;
    }
    
    /**
     * Returns port name
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }
    
    /**
     * Returns device formated name
     *
     * @return string
     */
    public function getDevice()
    {
        return $this->device;
    }
    
    /**
     * Sets the length of a character.
     * length of a character (5 <= length <= 8)
     *
     * @param  int $length
     * @return boolean
     */
    public function setDataBits($length)
    {
        if ($length < 5 || $length > 8) {
            $length = 8;
        }
        $this->databits = $length;
        $this->formatedDataBits = $this->zDataBits($length);
        return true;
    }
    
    /**
     * Returns char length
     *
     * @return int
     */
    public function getDataBits()
    {
        return $this->databits;
    }
    
    /**
     * Format data bits commands
     *
     * @param  int $length
     * @return string
     */
    protected function zDataBits($length)
    {
        $fdatabits = "cs$length";
        if ($this->ostype == self::OS_WIN) {
            //windows
            $fdatabits = "DATA=$length";
        }
        return $fdatabits;
    }

    /**
     * Set serial baud rate
     *
     * @param  int $rate
     * @return boolean
     */
    public function setBaudRate($rate)
    {
        if (! isset($this->baudsargs[$rate])) {
            $rate = 9600;
        }
        $this->baudrate = $rate;
        $this->formatedBaudRate = $this->zBaudRate($rate);
        return true;
    }
    
    /**
     * Return baud rate
     *
     * @return int
     */
    public function getBaudRate()
    {
        return $this->baudrate;
    }
    
    /**
     * Format baud rate command
     *
     * @param  int $rate
     * @return string
     */
    protected function zBaudRate($rate)
    {
        $baud = "$rate";
        if ($this->ostype == self::OS_WIN) {
            //windows
            $baud = "BAUD=".$this->baudsargs[$rate];
        }
        return $baud;
    }


    /**
     * Sets parity mode
     *
     * @param  string $parity odd, even, none
     * @return boolean
     */
    public function setParity($parity)
    {
        if (! isset($this->parityargs[$parity])) {
            $parity = 'none';
        }
        $this->parity = $this->parityargs[$parity][0];
        $this->formatedParity = $this->zParity($parity);
        return true;
    }
    
    /**
     * Get parity mode set
     *
     * @return string
     */
    public function getParity()
    {
        switch ($this->parity) {
            case 0:
                return 'none';
            case 1:
                return 'odd';
            case 2:
                return 'even';
        }
    }
    
    /**
     * Format parity command
     *
     * @param  string $parity
     * @return string
     */
    protected function zParity($parity)
    {
        $fparity = $this->parityargs[$parity][1];
        if ($this->ostype == self::OS_WIN) {
            //windows
            $fparity = "PARITY=" .  strtoupper(substr($parity, 0, 1));
        }
        return $fparity;
    }


    /**
     * Set length of stop bits
     * the length of a stop bit.
     * It must be either 1, 1.5 or 2.
     * 1.5 is not supported under linux and on some computers.
     *
     * @param  float $length
     * @return boolean
     */
    public function setStopBits($length)
    {
        if ($length !== 1 && $length !== 1.5 && $length !== 2) {
            $length = 1;
        }
        $this->stopbits = $length;
        $this->formatedStopBits = $this->zStopBits($length);
        return true;
    }
    
    /**
     * Return stop bits set
     *
     * @return float
     */
    public function getStopBits()
    {
        return $this->stopbits;
    }
    
    /**
     * Format stop bit command
     *
     * @param  float $length
     * @return string
     */
    public function zStopBits($length)
    {
        $stopb = (($length == 1) ? "-" : "") . "cstopb";
        if ($this->ostype === self::OS_WIN) {
            $stopb = "STOP=" . $length;
        }
        return $stopb;
    }
    
    /**
     * Set the flow control mode.
     * Availible modes :
     *   "none" : no flow control
     *   "rts/cts" : use RTS/CTS handshaking
     *   "xon/xoff" : use XON/XOFF protocol
     *
     * @param  string $flow
     * @return boolean
     */
    public function setFlowControl($flow)
    {
        switch ($flow) {
            case 'rts/cts':
                $this->flowcontrol = self::FLOW_RTSCTS;
                break;
            case 'xon/xoff':
                $this->flowcontrol = self::FLOW_XONXOFF;
                break;
            default:
                $this->flowcontrol = self::FLOW_NONE;
        }
        $this->formatedFlowControl = $this->zFlowControl($this->flowcontrol);
        return true;
    }
    
    /**
     * Returns flow control set
     *
     * @return string
     */
    public function getFlowControl()
    {
        switch ($this->flowcontrol) {
            case 0:
                return 'none';
            case 1:
                return 'rts/cts';
            case 2:
                return 'xon/xoff';
        }
    }
    
    /**
     * Return flow control command formated for OP type
     *
     * @param  int $flow
     * @return string
     */
    protected function zFlowControl($flow)
    {
        $modeos = [
            //windows
            self::OS_WIN => [
                "xon=off octs=off rts=on",
                "xon=off octs=on rts=hs",
                "xon=on octs=off rts=on"
            ],
            //linux
            self::OS_LINUX => [
                "clocal -crtscts -ixon -ixoff",
                "-clocal crtscts -ixon -ixoff",
                "-clocal -crtscts ixon ixoff"
            ],
            //cygwin
            self::OS_CYGWIN => [
                "clocal -crtscts -ixon -ixoff",
                "-clocal crtscts -ixon -ixoff",
                "-clocal -crtscts ixon ixoff"
            ],
            //unix
            self::OS_UNIX => [
                "clocal -crtscts -ixon -ixoff",
                "-clocal crtscts -ixon -ixoff",
                "-clocal -crtscts ixon ixoff"
            ],
            //bsd
            self::OS_BSD => [
                "clocal -crtscts -ixon -ixoff",
                "-clocal crtscts -ixon -ixoff",
                "-clocal -crtscts ixon ixoff"
            ],
            //macos
            self::OS_OSX => [
                "clocal -crtscts -ixon -ixoff",
                "-clocal crtscts -ixon -ixoff",
                "-clocal -crtscts ixon ixoff"
            ],
            //hpux
            self::OS_HPUX => [
                "clocal -crtscts -ixon -ixoff",
                "-clocal crtscts -ixon -ixoff",
                "-clocal -crtscts ixon ixoff"
            ]
        ];
        return (string) $modeos[$this->ostype][$flow];
    }
    
    /**
     * Find OS type
     *
     * @return int
     */
    protected function getOs()
    {
        $oss = strtoupper(substr(PHP_OS, 0, 3));
        switch ($oss) {
            case 'DAR':
                return self::OS_OSX;
            case 'WIN':
                return self::OS_WIN;
            case 'LIN':
                return self::OS_LINUX;
            case 'CYG':
                return self::OS_CYGWIN;
            case 'HPU':
                return self::OS_HPUX;
            case 'BSD':
                return self::OS_BSD; //este esta incorreto
            case 'UNI':
                return self::OS_UNIX;
            default:
                return self::OS_UNKNOWN;
        }
    }
    
    /**
     * Exec command line in OS console
     *
     * @param  string $cmd comand line to execute
     * @param  array  $out retorn of this command in terminal
     * @return int
     */
    public function execCommand($cmd, &$out = null)
    {
        $desc = array(
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );
        $proc = proc_open($cmd, $desc, $pipes);
        $ret = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $retVal = proc_close($proc);
        if (func_num_args() == 2) {
            $out = array($ret, $err);
        }
        return $retVal;
    }
}
