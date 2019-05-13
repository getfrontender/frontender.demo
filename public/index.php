<?php

/**
 * @package     Dipity
 * @copyright   Copyright (C) 2014 - 2017 Dipity B.V. All rights reserved.
 * @link        http://www.dipity.eu
 */

defined('ROOT_PATH') || define('ROOT_PATH', dirname(__DIR__));

require '../vendor/autoload.php';

$platform = new \Frontender\Core\App();
$platform
    ->init()
    ->start();
