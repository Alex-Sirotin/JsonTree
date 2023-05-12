<?php


use PHPUnit\Framework\TestCase;

class JsonLoaderTest extends JsonTreeTestCase
{
    public function testLoadFromFile()
    {
        $loader = new \ABCShip\JsonTree\JsonLoader();
        foreach ($loader->loadFromFile('../data/data.json') as $line) {
            $this->assertJson($line);
        }
    }
}
