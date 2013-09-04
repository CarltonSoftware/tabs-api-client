<?php

/**
 * Tabs API Client autoloader.
 *
 * PHP Version 5.3
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */

namespace tabs;
define('TABSAPIDS', DIRECTORY_SEPARATOR);

spl_autoload_extensions(".class.php");

spl_autoload_register(
    function ($pClassName) {
        $file = str_replace('\\', TABSAPIDS, $pClassName) . '.class.php';
        $file = dirname(__FILE__) . TABSAPIDS . '..' . TABSAPIDS . $file;
        if (file_exists($file)) {
            include $file;
        }
    }
);