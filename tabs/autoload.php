<?php

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