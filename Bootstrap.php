<?php

define('APPLICATION_PATH', realpath(dirname(__FILE__)));
define('APPLICATION_SRC_PATH', realpath(APPLICATION_PATH . '/src'));

include APPLICATION_PATH . '/vendor/autoload.php';

spl_autoload_register(function ($class) {
    include APPLICATION_SRC_PATH . '/' . $class . '.php';
});