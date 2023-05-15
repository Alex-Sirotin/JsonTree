<?php

namespace ABCship\JsonTree\Tree;

interface TreeNodeInterface
{
//    /**
//     * @param ITreeNode $childNode
//     * @return void
//     */
//    public function addChild(ITreeNode $childNode): void;
//
//    /**
//     * @return ITreeNode[]
//     */
//    public function getChildren(): array;
//
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return int
     */
    public function getParentId(): int;
//
//    /**
//     * @param int $id
//     * @return bool
//     */
//    public function equal(int $id): bool;
//
////    public function getParentId(): int;
//
//    /**
//     * @return ITreeNode
//     */
//    public function getParent(): ITreeNode;
}