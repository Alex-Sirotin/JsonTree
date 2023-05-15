<?php

namespace ABCship\JsonTree\StoreProvider\File;

use ABCship\Application\Utils\Memory;
use ABCship\JsonTree\StoreProvider\StoreProviderInterface;
use ABCship\JsonTree\Tree\TreeNode;
use ABCship\JsonTree\Tree\TreeNodeInterface;
use SplFileObject;
use SplTempFileObject;

class Provider /*extends AbstractStoreProvider*/ implements StoreProviderInterface
{
    use Memory;

    const MAX_PAGE_COUNT = 1024;
    const DEFAULT_PAGE_COUNT = 128;
    private int $pageCount;
    private array $pages = [];
    private array $parentPages = [];

    public function __construct(?int $pageCount = null)
    {
        $this->pageCount = $this->calcPageCount($pageCount);
    }

    /**
     * @param int|null $pageCount
     * @return int
     */
    private function calcPageCount(?int $pageCount): int
    {
        if (!$pageCount) {
            $pageCount = self::DEFAULT_PAGE_COUNT;
        }
        if ($pageCount > self::MAX_PAGE_COUNT) {
            $pageCount = self::MAX_PAGE_COUNT;
        }

        return $pageCount;
    }

    /**
     * @param int $id
     * @return int
     */
    private function getPageIndex(int $id): int
    {
        return $id % $this->pageCount;
    }

    /**
     * @param int $id
     * @param int|null $parentId
     * @param string $name
     * @return void
     */
    private function addToPage(int $id, ?int $parentId, string $name): void
    {
        $pageNum = $this->getPageIndex($id);
        if (!isset($this->pages[$pageNum])) {
            $this->pages[$pageNum] = new SplTempFileObject();
            $this->pages[$pageNum]->setFlags(SplFileObject::READ_CSV);
        }
        $this->pages[$pageNum]->fputcsv([$id, $parentId, $name]);
    }

    /**
     * @param int $id
     * @param int|null $parentId
     * @return void
     */
    private function addToIndex(int $id, ?int $parentId): void
    {
        $parentNum = $this->getPageIndex(($parentId ? : 0));
        if (!isset($this->parentPages[$parentNum])) {
            $this->parentPages[$parentNum] = new SplTempFileObject();
            $this->parentPages[$parentNum]->setFlags(SplFileObject::READ_CSV);
        }
        $this->parentPages[$parentNum]->fputcsv([$id, $parentId]);
    }

    public function search(int $id): ?TreeNodeInterface
    {
        $page = $this->getPageIndex($id);
        $file = $this->pages[$page] ?? false;
        if (!$file) {
            return null;
        }
        $file->rewind();
        while (!$file->eof()) {
            list($foundId, $foundParentId, $foundName) = $file->fgetcsv();
            if ((int)$foundId === $id) {
                return $this->get(
                    $foundId,
                    $foundName,
                    is_null($foundParentId) ? null : (int)$foundParentId

                );
            }
        }

        return null;
    }

    public function searchTree(int $nodeId): ?iterable
    {
        $node = $this->search($nodeId);
        yield $node;
        if (!$node) {
            return null;
        }
        while ($node->getParentId()) {
            $node = $this->search($node->getParentId());
            yield $node;
        }
    }

    public function traverseDepthFirst(callable $callback, int $rootId): void
    {
        $node = $this->search($rootId);
        $callback($node);
        foreach ($this->getChildren($node->getId()) as $childId) {
            $this->traverseDepthFirst($callback, $childId);
        }
    }

    public function add(int $id, string $name, ?int $parentId): TreeNodeInterface
    {
        $this->addToPage($id, $parentId, $name);
        $this->addToIndex($id, $parentId);

        return $this->get($id, $name, $parentId);
    }

    private function getChildren(int $nodeId): ?iterable
    {
        $page = $this->getPageIndex($nodeId);
        $file = $this->parentPages[$page] ?? false;
        if (!$file) {
            return null;
        }
        $file->rewind();
        while (!$file->eof()) {
            list($foundId, $foundParentId) = $file->fgetcsv();
            if ((int)$foundParentId === $nodeId) {
                yield $foundId;
            }
        }
    }

    public function get(int $id, string $name, ?int $parentId): TreeNodeInterface
    {
        $children = $this->getChildren($id);
        return new TreeNode($id, $name, $parentId, $children);
    }
//
//    public function addChild($parentId, TreeNodeInterface $node): Node
//    {
//        return $this->create($node->getId(), $node->getName(), $parentId);
//    }

    /**
     * @return iterable
     */
    public function getData(): iterable
    {
        foreach ($this->pages as $file) {
            $file->rewind();
            while (!$file->eof()) {
                list($id, $parent, $name) = $file->fgetcsv();
                yield [
                    'id' => $id,
                    'name' => $name,
                    'parent_id' => $parent,
                ];
            }
        }
    }

    function prepare(): void
    {
        $this->pages = [];
        $this->parentPages = [];
    }
}
