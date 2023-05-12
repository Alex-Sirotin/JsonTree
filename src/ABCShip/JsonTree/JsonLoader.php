<?php

namespace ABCShip\JsonTree;

use Generator;
use Exception;

class JsonLoader
{
    /**
     * @param string $filename
     * @return Generator
     * @throws Exception
     */
    public function loadFromFile(string $filename): Generator
    {
        $fileInfo = new \SplFileInfo($filename);
        if (!$fileInfo->isFile()) {
            throw new \Exception("File '{$file}' does not exist!");
        }

        if (!$fileInfo->isReadable()) {
            throw new \Exception("Can't open file '{$file}'!");
        }

        $file = new \SplFileObject($fileInfo->getPathname());

        while (!$file->eof()) {
            $line = trim(trim($file->fgets()), ",[]");
            if ($line) {
                yield $line;
            }
        }
    }
}
