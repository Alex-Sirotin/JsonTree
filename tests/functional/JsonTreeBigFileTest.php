<?php

namespace ABCship\Tests\functional;

use ABCship\Application\FileGenerator;
use ABCship\JsonTree\DataLoader\JsonLoader;
use ABCship\JsonTree\StoreProvider\File\Provider;
use ABCship\JsonTree\Tree\Tree;
use ABCship\Tests\JsonTreeTestCase;
use Exception;

class JsonTreeBigFileTest extends JsonTreeTestCase
{
    protected static ?Tree $bigTree;

    /**
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $file = FileGenerator::generateFile(1_000_000);
        $loader = new JsonLoader();
        $storeProvider = new Provider(100);
        self::$bigTree = new Tree($storeProvider);
        self::$bigTree->buildTree($loader->loadFromFile($file));
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::$bigTree = null;
    }

    public function testTraverse()
    {
        $result = 0;
        self::$bigTree->traverseDepthFirst(function ($node) use (&$result) {
            $result += $node->getId();
        });
        $this->assertTrue(true);
    }

    public function testSearch()
    {
        echo 'testSearch' . PHP_EOL;
        $node = self::$bigTree->search(999999);
        $this->assertEquals(999999, $node->getId());
        echo $node . PHP_EOL;

        $node = self::$bigTree->search(1);
        $this->assertEquals(1, $node->getId());
        echo $node . PHP_EOL;

        $node = self::$bigTree->search(45678);
        $this->assertEquals(45678, $node->getId());
        echo $node . PHP_EOL;
        echo PHP_EOL;
        ob_flush();
    }

    public function testSearchTree()
    {
        echo 'testSearchTree' . PHP_EOL;
        $path = self::$bigTree->searchTree(999999);
        $this->assertIsIterable($path);
        foreach($path as $node) {
            echo $node . PHP_EOL;
        }
        echo PHP_EOL;
        ob_flush();
    }
}
