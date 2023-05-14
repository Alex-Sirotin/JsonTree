<?php

namespace ABCship\JsonTree\DataLoader;

use Exception;
use SplFileInfo;
use SplFileObject;

class JsonLoader implements DataLoaderInterface
{
    /**
     * @param string $filename
     * @return iterable
     * @throws Exception
     */
    public function loadFromFile(string $filename): iterable
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
