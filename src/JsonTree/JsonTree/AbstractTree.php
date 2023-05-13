<?php

namespace ABCship\JsonTree;

abstract class AbstractTree extends AbstractTreeNode
{
    /**
     * @param  int $id
     * @return AbstractTreeNode|null
     */
    public function search(int $id): ?ITreeNode
    {
        foreach ($this->getChildren() as $item) {
            if ($item->equal($id)) {
                return $item;
            } else {
                return $item->seacrh($id);
            }
        }

        return null;
    }

    /**
     * @param  ITreeNode $node
     * @return ITreeNode[]|null
     */
    protected function getParents(ITreeNode $node): ?array
    {
        $parent = $node->getParent();
        if (!$parent) {
            return null;
        }

        $parentNodes = $parent->getParents($parent);
        if (!$parentNodes) {
            return null;
        }

        return array_merge([$parent], $parentNodes);
    }

    /**
     * @param  int $nodeId
     * @return ITreeNode[]
     */
    public function searchTree(int $nodeId): array
    {
        $result = [];
        $item = $this->search($nodeId);
        if ($item) {
            $result[] = $item;
            $item->getParents($item);
        }

        return $result;
    }

    /**
     * @param  callable $callback
     * @return void
     */
    public function traverseDepthFirst(callable $callback): void
    {
        foreach ($this->getChildren() as $item) {
            $callback($item);
            $item->traverseDepthFirst($callback);
        }
    }

    /**
     * @param ITreeNode[] $data
     * @return bool
     */
    function buildTree(iterable $data): bool
    {
      foreach ($data as $item) {
        $node = $this->createNode($item);
        $this->addChild($node);
      }
    }

    /**
     * @return ITreeNode[]
     */
    abstract function flattenTree(): array;
}
