<?php

namespace Posprint\Connectors;

/**
 * Classe USB
 * 
 * @category   NFePHP
 * @package    Posprint
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/posprint for the canonical source repository
 */

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
