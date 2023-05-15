<?php

namespace ABCship\Tests;

use ABCship\JsonTree\StoreProvider\File\Provider;
use ABCship\JsonTree\Tree\Tree;
use PHPUnit\Framework\TestCase;

class JsonTreeTestCase extends TestCase
{

    protected Tree $tree;

    public function createTree()
    {
        $storeProvider = new Provider(7);
        $this->tree = new Tree($storeProvider);
        $this->tree->buildTree($this->getData());
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
            '{ "id": 5, "name": "Node 5", "parent_id": 2 }',
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
