<?php

namespace ABCShip\JsonTree;

class TreeNode extends AbstractTreeNode
{
    public function __construct(int $id, string $name, int $parentId)
    {
        $this->id = $id;
        $this->name = $name();
        $parent = $this->search($parentId);
        if ($parent) {
            $this->setParent($parent);
        }
    }

    function equal(int $id): bool
    {
        // TODO: Implement equal() method.
    }

    public function getChildren(): array
    {
        // TODO: Implement getChildren() method.
    }
}