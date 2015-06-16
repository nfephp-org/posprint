<?php
namespace Posprint\Connectors;

use Posprint\Connectors\Connector;
use Posprint\Connectors\File;
use Exception;

//sudo chown www-data:lpadmin /dev/usb/lp0
//sudo usermod -a -G lpadmin www-data

//net use LPT2 \\<machine>\<printer share> /yes
//net use lpt2 \\acer-6e395d0925\LexmarkP /persistent:yes
// NET USE LPT1: \\[Computer Name]\Printer /PERSISTENT:YES


class Usb extends File implements Connector
{

}
