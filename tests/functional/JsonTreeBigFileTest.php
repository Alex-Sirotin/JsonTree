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

    const ROW_COUNT = 1_000_000;

    /**
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $file = FileGenerator::generateFile(self::ROW_COUNT);
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
        $node = self::$bigTree->search(self::ROW_COUNT-1);
        $this->assertEquals(self::ROW_COUNT-1, $node->getId());
        echo $node . PHP_EOL;

        $node = self::$bigTree->search(1);
        $this->assertEquals(1, $node->getId());
        echo $node . PHP_EOL;

        $id = rand(1, self::ROW_COUNT);
        $node = self::$bigTree->search($id);
        $this->assertEquals($id, $node->getId());
        echo $node . PHP_EOL;
        echo PHP_EOL;
        ob_flush();
    }

    public function testSearchTree()
    {
        echo 'testSearchTree' . PHP_EOL;
        $path = self::$bigTree->searchTree(self::ROW_COUNT-1);
        $this->assertIsIterable($path);
        foreach($path as $node) {
            echo $node . PHP_EOL;
        }
        echo PHP_EOL;
        ob_flush();
    }
}
