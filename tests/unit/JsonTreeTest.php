<?php

namespace ABCship\Tests\unit;

use ABCship\Application\Utils\Memory;
use ABCship\JsonTree\Tree\TreeNodeInterface;
use ABCship\Tests\JsonTreeTestCase;
use Exception;

class JsonTreeTest extends JsonTreeTestCase
{
    use Memory;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTree();
    }

    public function testBuildTree()
    {
        $this->assertIsObject($this->tree);
    }

    public function testFlattenTree()
    {
        $result = [
            [1, "Node 1", null],
            [2, "Node 2", 1],
            [5, "Node 5", 2],
            [10, "Node 10",5],
            [9, "Node 9", 10],
            [3, "Node 3", 1],
            [4, "Node 4", 3],
            [6, "Node 6", 4],
            [7, "Node 7", 4],
            [8, "Node 8", 7],
        ];

        $this->assertIsIterable($this->tree->flattenTree());
        $count = 0;
        foreach ($this->tree->flattenTree() as $row) {
            list('name' => $name, 'id' => $id, 'parent_id' => $parent) = $row;
            list($id2, $name2, $parent2) = current($result);
            $this->assertEquals($id2, $id);
            $this->assertEquals($parent2, $parent);
            $this->assertEquals($name2, $name);
            next($result);
            $count++;
        }
        $this->assertEquals(count($result), $count);
    }

    public function testSearch()
    {
        $found = $this->tree->search(5);
        $this->assertNotNull($found);
        $this->assertInstanceOf(TreeNodeInterface::class, $found) ;
        $this->assertEquals(5, $found->getId());

        $found = $this->tree->search(1);
        $this->assertNotNull($found);
        $this->assertInstanceOf(TreeNodeInterface::class, $found) ;
        $this->assertEquals(1, $found->getId());

        $this->assertNull($this->tree->search(12));
    }

    public function testSearchTree()
    {
        $result = [
            [9, "Node 9", 10],
            [10, "Node 10",5],
            [5, "Node 5", 2],
            [2, "Node 2", 1],
            [1, "Node 1", null],
        ];

        $found = $this->tree->searchTree(9);
        $this->assertNotNull($found);
        $this->assertIsIterable($found);
        foreach ($found as $node) {
            $expected = current($result);
            $actual = array_values($node->toArray());
            $this->assertEqualsCanonicalizing($expected, $actual);
            next($result);
        }
    }

    public function testTraverseDeepFirst()
    {
        $result = 0;
        $this->tree->traverseDepthFirst(function ($node) use (&$result) {
            $result += $node->getId();
        });

        $this->assertEquals(55, $result);
    }
}
