<?php

namespace ABCship\Application\Utils;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

trait Log
{
    public const DEFAULT_LOG_NAME = 'common';
    public const DEFAULT_LOG_PATH = APPLICATION_PATH . '/logs/';
    public const DEFAULT_LOG_FILE = self::DEFAULT_LOG_PATH . self::DEFAULT_LOG_NAME . '.log';
    private static array $logger;
    public function getLogger(?string $name, ?string $file = null, Level $logLevel = Level::Warning)
    {
        if (!$name) {
            $name = self::DEFAULT_LOG_NAME;
        }
        if (!$file) {
            $file = self::DEFAULT_LOG_FILE;
        }

        if (!is_dir(self::DEFAULT_LOG_FILE)) {
            mkdir(self::DEFAULT_LOG_FILE);
        }

        if (self::$logger[$name] ?? false) {
            return self::$logger[$name];
        }
        self::$logger[$name] = new Logger($name);
        self::$logger[$name]->pushHandler(new StreamHandler($file, $logLevel));

        return self::$logger[$name];
    }

    public function error(string $msg, ?string $name = null): void
    {
        $logger = $this->getLogger($name);
        $logger->error($msg);
    }
}
