<?php

namespace ABCShip\JsonTree;

use ABCShip\Util\Memory;
use Generator;
use SplTempFileObject;
use SplFileObject;
use ABCShip\JsonTree\StoreProvider\StoreProviderInterface;

class Tree
{
    use Memory;
//
//    const MAX_PAGE_COUNT = 128;

//    private int $count = 0;
//    private int $itemSize = 0;
//    private int $pageCount = 1;
//
//    private array $pages = [];
//    private array $parentPages = [];
    private SplTempFileObject $errors;

    private StoreProviderInterface $storeProvider;

    public function __construct(StoreProviderInterface $storeProvider)
    {
        $this->prepare();
        $this->storeProvider = $storeProvider;
    }

//    /**
//     * @param iterable $data
//     * @param int|null $pageCount
//     * @return int
//     */
//    private function calcPageCount(iterable $data, ?int $pageCount = null): int
//    {
//        if ($pageCount) {
//            $this->pageCount = $pageCount;
//            return $this->pageCount;
//        }
//
//        $this->calcSize($data);
//        $memoryLimit = intdiv($this->getMemoryLimit(), 2);
//        $pageSize = intdiv($memoryLimit, $this->itemSize);
//        $this->pageCount = intdiv($this->count, $pageSize) ? : 1;
//
//        if ($this->pageCount > self::MAX_PAGE_COUNT) {
//            $this->pageCount = self::MAX_PAGE_COUNT;
//        }
//
//        return $this->pageCount;
//    }
//
//    /**
//     * @param iterable $data
//     * @return int
//     */
//    private function calcSize(iterable $data): int
//    {
//        $this->count = 0;
//        $size = 0;
//        foreach ($data as $item) {
//            $this->count++;
//            $size += strlen($item);
//        }
//
//        $this->itemSize = (int)($size / $this->count);
//
//        return $this->itemSize;
//    }
//
//    /**
//     * @param int $id
//     * @return int
//     */
//    private function getPageIndex(int $id): int
//    {
//        return $id % $this->pageCount;
//    }
//
//    /**
//     * @param int $id
//     * @param int|null $parentId
//     * @param string $name
//     * @return void
//     */
//    private function addToPage(int $id, ?int $parentId, string $name): void
//    {
//        $pageNum = $this->getPageIndex($id);
//        if (!isset($this->pages[$pageNum])) {
//            $this->pages[$pageNum] = new SplTempFileObject();
//            $this->pages[$pageNum]->setFlags(SplFileObject::READ_CSV);
//        }
//        $this->pages[$pageNum]->fputcsv([$id, $parentId, $name]);
//    }
//
//    /**
//     * @param int $id
//     * @param int|null $parentId
//     * @return void
//     */
//    private function addToIndex(int $id, ?int $parentId): void
//    {
//        $parentNum = $this->getPageIndex(($parentId ? : 0));
//        if (!isset($this->parentPages[$parentNum])) {
//            $this->parentPages[$parentNum] = new SplTempFileObject();
//            $this->parentPages[$parentNum]->setFlags(SplFileObject::READ_CSV);
//        }
//        $this->parentPages[$parentNum]->fputcsv([$id]);
//    }

    /**
     * @return void
     */
    private function prepare(): void
    {
//        $this->count = 0;
//        $this->itemSize = 0;
//        $this->pageCount = 1;
//
//        $this->pages = [];
//        $this->parentPages = [];
        $this->errors = new SplTempFileObject();
        $this->errors->setFlags(SplFileObject::DROP_NEW_LINE);
    }

    /**
     * @param iterable $data
     * @param int|null $pageCount
     * @return void
     */
    public function buildTree(iterable $data, ?int $pageCount = null): void
    {
        $this->prepare();

        $this->calcPageCount($data, $pageCount);

        foreach ($data as $item) {

            list($id, $parentId, $name) = $this->parse($item);
            if (!$id) {
                continue;
            }
            $this->addToPage($id, $parentId, $name);
            $this->addToIndex($id, $parentId);
        }
    }

    /**
     * @param string $item
     * @return array|null[]
     */
    private function parse(string $item): array
    {
        list('id' => $id, 'parent_id' => $parentId, 'name' => $name) = json_decode($item, true);

        if (!$id || !$name) {
            $this->addError($item);
            return [null];
        }

        return [$id, $parentId, $name];
    }

    /**
     * @return Generator
     */
    public function flattenTree(): Generator
    {
        foreach ($this->storeProvider->getData() as $item) {
            yield $item;
        }
//        foreach($this->pages as $file)
//        {
//            $file->rewind();
//            while(!$file->eof()) {
//                list($id, $parent, $name) = $file->fgetcsv();
//                yield [
//                    'id' => $id,
//                    'name' => $name,
//                    'parent_id' => $parent,
//                ];
//            }
//        }
    }

    /**
     * @param string $item
     * @return bool
     */
    private function addError(string $item): bool
    {
        return $this->errors->fwrite($item . "\n");
    }

    /**
     * @return Generator
     */
    public function getBuildErrors(): Generator
    {
        $this->errors->rewind();
        while(!$this->errors->eof()) {
            yield $this->errors->fgets();
        }
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function search(int $id): ITreeNode
    {
        return $this->storeProvider->search($id);

//        $page = $this->getPageIndex($id);
//        $file = $this->pages[$page];
//        $file->rewind();
//        while(!$file->eof()) {
//            list($foundId, $foundParentId, $foundName) = $file->fgetcsv();
//            if ((int)$foundId === $id) {
//                return [
//                    (int)$foundId,
//                    is_null($foundParentId) ? null : (int)$foundParentId,
//                    $foundName
//                ];
//            }
//        }
//
//        return null;
    }
}
