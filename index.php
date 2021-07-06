<?php
/**
 * @copyright 2021
 * @license   All rights reserved
 */

declare(strict_types = 1);

use Application\Autoloader;
use Application\Service\ServDb;

require 'Application/Autoloader.php';
Autoloader::register();

$servDb = new ServDb();

if(isset($_GET['init'])) {
    $servDb->init();
} else {
    $servDb->run();
}
