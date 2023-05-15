<?php

namespace ABCship\Tests\unit;

use ABCship\Application\Utils\Memory;
use ABCship\JsonTree\StoreProvider\File\Provider;
use ABCship\JsonTree\Tree;
use ABCship\Tests\JsonTreeTestCase;
use Exception;

class JsonTreeTest extends JsonTreeTestCase
{
    use Memory;

    private Tree $tree;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $storeProvider = new Provider(7);
        $this->tree = new Tree($storeProvider);
        $this->tree->buildTree($this->getData());
    }

    public function testBuildTree()
    {
        $this->assertIsObject($this->tree);
    }

    public function testFlattenTree()
    {
        $this->tree->buildTree($this->getData());

        $result = [
            [1, "Node 1", null],
            [8, "Node 8", 7],
            [2, "Node 2", 1],
            [9, "Node 9", 10],
            [3, "Node 3", 1],
            [10, "Node 10",5],
            [4, "Node 4", 3],
            [11, "Node 10"],
            [5, "Node 5", 5],
            [6, "Node 6", 4],
            [7, "Node 7", 4],
        ];

        $this->assertIsIterable($this->tree->flattenTree());
        foreach($this->tree->flattenTree() as $row) {
            list('name' => $name, 'id' => $id, 'parent_id' => $parent) = $row;
            list($id2, , $parent2) = current($result);
            $this->assertEquals($id2, $id);
            $this->assertEquals($parent2, $parent);
            $this->assertEquals($name, $name);
            next($result);
        }
    }

    public function testSearch()
    {
        $found = $this->tree->search(5);
        $this->assertNotNull($found);
        $this->assertInstanceOf(Tree\TreeNodeInterface::class, $found) ;
        $this->assertEquals(5, $found->getId());

        $found = $this->tree->search(1);
        $this->assertNotNull($found);
        $this->assertInstanceOf(Tree\TreeNodeInterface::class, $found) ;
        $this->assertEquals(1, $found->getId());

        $this->assertNull($this->tree->search(12));
    }

    public function getData(): array
    {
        return [
            'gfdgsdfgdf',
            '{ "id": 1, "name": "Node 1", "parent_id": null }',
            'gfdgsdfgdf',
            '{ "id": 2, "name": "Node 2", "parent_id": 1 }',
            '{ "id": 3, "name": "Node 3", "parent_id": 1 }',
            '{ "id": 4, "name": "Node 4", "parent_id": 3 }',
            '{ "id": 5, "name": "Node 5", "parent_id": 5 }',
            '{ "id": 6, "name": "Node 6", "parent_id": 4 }',
            '{ "id": 7, "name": "Node 7", "parent_id": 4 }',
            '{ "id": 8, "name": "Node 8", "parent_id": 7 }',
            '{ "id": 9, "name": "Node 9", "parent_id": 10 }',
            '{ "id": 10, "name": "Node 10", "parent_id": 5 }',
            '{ "id": 11, "name": "Node 10" }',
            '{ "id1": 12, "name2": "Node 10" }',
            '[ "id1": 13, "name2": "Node 10" }',
        ];
    }
}

