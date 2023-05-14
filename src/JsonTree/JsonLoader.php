<?php

namespace ABCship\JsonTree;

use Generator;
use Exception;
use SplFileInfo;
use SplFileObject;

class JsonLoader implements DataLoaderInterface
{
    /**
     * @param string $filename
     * @return Generator
     * @throws Exception
     */
    public function loadFromFile(string $filename): Generator
    {
        $fileInfo = new SplFileInfo($filename);
        if (!$fileInfo->isFile()) {
            throw new Exception("File '{$filename}' does not exist!");
        }

        if (!$fileInfo->isReadable()) {
            throw new Exception("Can't open file '{$filename}'!");
        }

        $file = new SplFileObject($fileInfo->getPathname());

        while (!$file->eof()) {
            $line = trim(trim($file->fgets()), ",[]");
            if ($line) {
                yield $line;
            }
        }
    }
}
