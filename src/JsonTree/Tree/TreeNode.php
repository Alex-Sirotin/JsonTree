<?php

namespace ABCship\JsonTree\Tree;

class TreeNode extends AbstractTreeNode implements TreeNodeInterface
{
    public function __construct(int $id, string $name, ?int $parentId = null, ?iterable $children = null)
    {
        parent::__construct($id, $name, $parentId);
        $this->children = $children;
//        $parent = $this->search($parentId);
//        if ($parent) {
//            $this->setParent($parent);
//        }
    }
//
//    function equal(int $id): bool
//    {
//        // TODO: Implement equal() method.
//    }
//
//    public function getChildren(): array
//    {
//        // TODO: Implement getChildren() method.
//    }
//
//    public function getParentId(): int
//    {
//        // TODO: Implement getParentId() method.
//    }
}