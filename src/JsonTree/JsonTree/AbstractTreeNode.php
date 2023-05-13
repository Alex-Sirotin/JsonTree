<?php

namespace ABCship\JsonTree;

abstract class AbstractTreeNode implements ITreeNode
{
    /** @var int */
    protected int $id;

    /** @var ITreeNode */
    protected ITreeNode $parent;

    /** @var string */
    protected string $name;
    
    /** @var ITreeNode[] */
    protected array $children = [];

    /**
     * @return ITreeNode
     */
    public function getParent(): ITreeNode
    {
        return $this->parent;
    }
    /**
     * @param ITreeNode $node
     */
    protected function setParent(ITreeNode $node): void
    {
        $this->parent = $node;
    }

    /**
     * @param ITreeNode $childNode
     */
    public function addChild(ITreeNode $childNode): void
    {
        $childNode->setParent($this);
        $this->children[] = $childNode;
    }

//    /**
//     * @return AbstractTreeNode[]
//     */
//    public function getChildren(): array
//    {
//        return $this->children;
//    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return !!$this->getParent();
    }

    /**
     * @return bool
     */
    public function isLeaf(): bool
    {
        return !$this->getChildren();
    }

    abstract function equal(int $id): bool;

//    public static function create(ITreeNode $node): AbstractTreeNode
//    {
//        $result = new self($node->getId(), $node->getName(), $node->getParentId());
//
//        return $result;
//    }
}
