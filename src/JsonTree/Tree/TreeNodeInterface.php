<?php

namespace ABCship\JsonTree\Tree;

interface TreeNodeInterface
{
    /**
     * @param TreeNodeInterface $childNode
     * @return TreeNodeInterface
     */
    public function addChild(TreeNodeInterface $childNode): TreeNodeInterface;

    /**
     * @return TreeNodeInterface[]
     */
    public function getChildren(): iterable;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return int|null
     */
    public function getParentId(): ?int;
}