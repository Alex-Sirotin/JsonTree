<?php

namespace ABCship\JsonTree\Tree;

use Stringable;

abstract class AbstractTreeNode implements TreeNodeInterface, Stringable
{
    /** @var int */
    protected int $id;

    /** @var int */
    protected ?int $parentId = null;

    /** @var TreeNodeInterface */
    protected TreeNodeInterface $parent;

    /** @var string */
    protected string $name;

    /** @var TreeNodeInterface[] */
    protected iterable $children;

    /**
     * @param int $id
     * @param string $name
     * @param int|null $parentId
     */
    public function __construct(int $id, string $name, ?int $parentId = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->parentId = $parentId;
    }

//    /**
//     * @return TreeNodeInterface
//     */
//    public function getParent(): TreeNodeInterface
//    {
//        return $this->parent;
//    }
    /**
     * @param TreeNodeInterface $node
     */
    protected function setParent(TreeNodeInterface $node): void
    {
        $this->parent = $node;
    }

//    /**
//     * @param TreeNodeInterface $childNode
//     */
//    public function addChild(TreeNodeInterface $childNode): void
//    {
//        $childNode->setParent($this);
//        $this->children[] = $childNode;
//    }

    /**
     * @return TreeNodeInterface[]
     */
    public function getChildren(): iterable
    {
        return $this->children;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

//    /**
//     * @return bool
//     */
//    public function isRoot(): bool
//    {
//        return !!$this->getParent();
//    }
//
//    /**
//     * @return bool
//     */
//    public function isLeaf(): bool
//    {
//        return !$this->getChildren();
//    }
//
//    abstract function equal(int $id): bool;
//
//    public static function create(ITreeNode $node): AbstractTreeNode
//    {
//        $result = new self($node->getId(), $node->getName(), $node->getParentId());
//
//        return $result;
//    }

    public function __toString()
    {
        return "ID: {$this->getId()}, ParentID: {$this->getParentId()}, name: {$this->getName()} ";
    }
}
