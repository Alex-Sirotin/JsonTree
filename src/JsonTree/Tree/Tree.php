<?php

namespace ABCship\JsonTree\Tree;

use ABCship\Application\Utils\Log;
use ABCship\JsonTree\StoreProvider\File\Provider;
use ABCship\JsonTree\StoreProvider\StoreProviderInterface;

class Tree
{
    use Log;

    private ?TreeNodeInterface $root = null;
    private StoreProviderInterface $storeProvider;

    public function __construct(?StoreProviderInterface $storeProvider)
    {
        $this->storeProvider = $storeProvider ? : new Provider();
    }

    private function prepare(): void
    {
        $this->root = null;
        $this->storeProvider->prepare();
    }

    /**
     * @param iterable $data
     * @return void
     */
    public function buildTree(iterable $data): void
    {
        $this->prepare();
        foreach ($data as $item) {
            list($id, $name, $parentId) = $this->parse($item);

            if (!$id) {
                continue;
            }

            if (!isset($parentId)) {
                if (isset($this->root)) {
                    $this->error("Too many roots {$this->root->getId()}, {$id}");
                    continue;
                }
                $this->root = $this->storeProvider->get($id, $name, $parentId);
            }

            $this->add($id, $name, $parentId);
        }
    }

    /**
     * @param callable $callback
     * @return iterable
     */
    public function traverseDepthFirst(callable $callback): void
    {
        foreach ($this->traverse($callback) as $item) {}
    }

    private function traverse(callable $callback): iterable
    {
        if (isset($this->root)) {
            return $this->storeProvider->traverseDepthFirst($callback, $this->root->getId());
        }
    }

    /**
     * @param string $item
     * @return array|null
     */
    private function parse(string $item): ?array
    {
        list('id' => $id, 'parent_id' => $parentId, 'name' => $name) = json_decode($item, true);

        if (!$id || !$name) {
            $this->error("Wrong item: '{$item}'");
            return null;
        }

        return [$id, $name, $parentId];
    }

    /**
     * @param int $id
     * @param string $name
     * @param int|null $parentId
     * @return TreeNodeInterface
     */
    public function add(int $id, string $name, ?int $parentId): TreeNodeInterface
    {
        return $this->storeProvider->add($id, $name, $parentId);
    }

    /**
     * @return iterable
     */
    public function flattenTree(): iterable
    {
        yield from $this->traverse(function($node) {
            return $node->toArray();
        });
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function search(int $id): ?TreeNodeInterface
    {
        return $this->storeProvider->search($id);
    }

    /**
     * @param int $nodeId
     * @return iterable
     */
    public function searchTree(int $nodeId): iterable
    {
        return $this->storeProvider->searchTree($nodeId);
    }
}
