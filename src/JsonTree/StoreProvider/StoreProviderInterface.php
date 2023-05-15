<?php

namespace ABCship\JsonTree\StoreProvider;

use ABCship\JsonTree\Tree\TreeNodeInterface;
use Generator;

interface StoreProviderInterface
{
    public function search(int $id): ?TreeNodeInterface;
    public function searchTree(int $nodeId): ?iterable;
    public function traverseDepthFirst(callable $callback, int $rootId): void;
    public function add(int $id, string $name, ?int $parentId): TreeNodeInterface;
    public function get(int $id, string $name, ?int $parentId): TreeNodeInterface;
    public function getData(): iterable;
    function prepare(): void;
}
