<?php

namespace ABCship\JsonTree\DataLoader;

use Generator;

interface DataLoaderInterface
{
    public function loadFromFile(string $filename): iterable;
}
