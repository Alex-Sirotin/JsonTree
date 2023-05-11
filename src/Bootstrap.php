<?php

define('APPLICATION_SRC_PATH', realpath(dirname(__FILE__)));
define('APPLICATION_PATH', realpath(APPLICATION_SRC_PATH . '/../'));
//define('APPLICATION_UTIL_PATH', realpath(APPLICATION_SRC_PATH . '/Util'));

include APPLICATION_PATH . '/vendor/autoload.php';

spl_autoload_register(function ($class) {
    include APPLICATION_SRC_PATH . '/' . $class . '.php';
//    include APPLICATION_UTIL_PATH . '/' . $class . '.php';
});