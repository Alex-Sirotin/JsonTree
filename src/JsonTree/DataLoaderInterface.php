<?php

namespace ABCship\JsonTree;

use Generator;

interface DataLoaderInterface
{
    public function loadFromFile(string $filename): Generator;
}
