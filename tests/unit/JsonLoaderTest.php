<?php

namespace ABCship\Tests\unit;

use ABCship\JsonTree\DataLoader\JsonLoader;
use ABCship\Tests\JsonTreeTestCase;
use Exception;

class JsonLoaderTest extends JsonTreeTestCase
{
    /**
     * @throws Exception
     */
    public function testLoadFromFile()
    {
        $loader = new JsonLoader();
        foreach ($loader->loadFromFile(APPLICATION_PATH . '/data/data.json') as $line) {
            $this->assertJson($line);
        }
    }
}
