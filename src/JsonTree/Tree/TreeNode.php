<?php

namespace ABCship\JsonTree\Tree;

use Stringable;

abstract class TreeNode implements TreeNodeInterface, Stringable
{
    /** @var int */
    protected int $id;

    /** @var ?int */
    protected ?int $parentId = null;

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
        $this->parentId = $parentId ? : null;
    }

    /**
     * @param TreeNodeInterface $node
     */
    protected function setParent(TreeNodeInterface $node): void
    {
        $this->parentId = $node->getId();
    }

    /**
     * @param TreeNodeInterface $childNode
     * @return TreeNodeInterface
     */
    public function addChild(TreeNodeInterface $childNode): TreeNodeInterface
    {
        $childNode->setParent($this);
        $this->children[] = $childNode;

        return $childNode;
    }

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
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return is_numeric($this->parentId) ? $this->parentId : null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $parentId = $this->getParentId() ? : 'null';
        return "ID: {$this->getId()}, ParentID: {$parentId}, name: {$this->getName()} ";
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'parent_id' => $this->getParentId(),
        ];
    }
}
