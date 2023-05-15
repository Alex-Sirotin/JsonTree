<?php

namespace ABCship\JsonTree;

use ABCship\Application\Utils\Log;
use ABCship\JsonTree\StoreProvider\File\Provider;
use ABCship\JsonTree\StoreProvider\StoreProviderInterface;
use ABCship\JsonTree\Tree\TreeNodeInterface;
use Generator;

class Tree
{
    use Log;

    private int $rootId;

    private StoreProviderInterface $storeProvider;

    public function __construct(?StoreProviderInterface $storeProvider)
    {
        $this->storeProvider = $storeProvider ? : new Provider();
    }

    private function prepare(): void
    {
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
            list($id, $parentId, $name) = $this->parse($item);

            if (!$id) {
                continue;
            }

            if (!isset($parentId)) {
                if (isset($this->rootId)) {
                    $this->error("Too many roots {$this->rootId}, {$id}");
                    continue;
                }
                $this->rootId = $id;
            }

            $this->add($id, $parentId, $name);
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
        return $this->storeProvider->getData();
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
