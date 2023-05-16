<?php

namespace ABCship\JsonTree\StoreProvider;

use ABCship\JsonTree\Tree\TreeNodeInterface;

interface StoreProviderInterface
{
    public function search(int $id): ?TreeNodeInterface;
    public function searchTree(int $nodeId): ?iterable;
    public function traverseDepthFirst(callable $callback, TreeNodeInterface $root): iterable;
    public function add(int $id, string $name, ?int $parentId): TreeNodeInterface;
    public function buildNode(int $id, string $name, ?int $parentId): TreeNodeInterface;
//    public function get(int $id, string $name, ?int $parentId): TreeNodeInterface;
//    public function getData(): iterable;
    function prepare(): void;
}
