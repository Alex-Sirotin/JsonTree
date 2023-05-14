<?php

namespace ABCship\Application;

require_once 'Bootstrap.php';

use ABCship\Application\Command\GenerateCommand;
use Symfony\Component\Console\Application;
//use ABCship\Application\Command\TreeCommand;

$app = new Application();

$app->add(new GenerateCommand());
//$app->add(new TreeCommand());

try {
    $app->run();
} catch (\Exception $ex) {

}


