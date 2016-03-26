<?php

namespace Posprint\Connectors;

/**
 * Class USB
 * Connects directly to the USB port set.
 * Note: You should not be forgotten providing the appropriate permissions,
 *       otherwise the PHP will not have the necessary permissions for
 *       writing on that port.
 *
 *  In unix like systems will look something:
 *      sudo chown www-data:lpadmin /dev/usb/lp0
 *      sudo usermod -a -G lpadmin www-data
 *  In windows system will look something:
 *      net use LPT2 \\<machine>\<printer share> /yes
 *      net use lpt2 \\acer-6e395d0925\LexmarkP /persistent:yes
 *      net use LPT1: \\[Computer Name]\Printer /PERSISTENT:YES
 *
 * @category  NFePHP
 * @package   Posprint
 * @copyright Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/posprint for the canonical source repository
 */

use Posprint\Connectors\ConnectorInterface;
use Posprint\Connectors\File;

class Usb extends File implements ConnectorInterface
{

}
