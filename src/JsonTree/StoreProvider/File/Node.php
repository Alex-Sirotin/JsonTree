<?php

namespace ABCship\JsonTree\StoreProvider\File;

use ABCship\JsonTree\StoreProvider\StoreProviderInterface;
use ABCship\JsonTree\Tree\TreeNode;
use ABCship\JsonTree\Tree\TreeNodeInterface;

class Node extends TreeNode implements TreeNodeInterface
{
    private StoreProviderInterface $provider;

    public function setProvider(StoreProviderInterface $provider): void
    {
        $this->provider = $provider;
    }

    public function getChildren(): iterable
    {
        $this->children = $this->provider->getChildren($this->getId());

        return $this->children;
    }

    public function addChild(TreeNodeInterface $childNode): TreeNodeInterface
    {
        parent::addChild($childNode);
        $childNode->setProvider($this->provider);
        return $this->provider->add($childNode->getId(), $childNode->getName(), $this->getId());
    }
}
