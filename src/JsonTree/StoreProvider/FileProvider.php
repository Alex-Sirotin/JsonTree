<?php

namespace ABCship\JsonTree\StoreProvider;

class FileProvider
{
    const MAX_PAGE_COUNT = 128;

    private int $count = 0;
    private int $itemSize = 0;
    private int $pageCount = 1;

    private array $pages = [];
    private array $parentPages = [];

    /**
     * @param iterable $data
     * @param int|null $pageCount
     * @return int
     */
    private function calcPageCount(iterable $data, ?int $pageCount = null): int
    {
        if ($pageCount) {
            $this->pageCount = $pageCount;
            return $this->pageCount;
        }

        $this->calcSize($data);
        $memoryLimit = intdiv($this->getMemoryLimit(), 2);
        $pageSize = intdiv($memoryLimit, $this->itemSize);
        $this->pageCount = intdiv($this->count, $pageSize) ? : 1;

        if ($this->pageCount > self::MAX_PAGE_COUNT) {
            $this->pageCount = self::MAX_PAGE_COUNT;
        }

        return $this->pageCount;
    }

    /**
     * @param iterable $data
     * @return int
     */
    private function calcSize(iterable $data): int
    {
        $this->count = 0;
        $size = 0;
        foreach ($data as $item) {
            $this->count++;
            $size += strlen($item);
        }

        $this->itemSize = (int)($size / $this->count);

        return $this->itemSize;
    }

    /**
     * @param int $id
     * @return int
     */
    private function getPageIndex(int $id): int
    {
        return $id % $this->pageCount;
    }

    /**
     * @param int $id
     * @param int|null $parentId
     * @param string $name
     * @return void
     */
    private function addToPage(int $id, ?int $parentId, string $name): void
    {
        $pageNum = $this->getPageIndex($id);
        if (!isset($this->pages[$pageNum])) {
            $this->pages[$pageNum] = new SplTempFileObject();
            $this->pages[$pageNum]->setFlags(SplFileObject::READ_CSV);
        }
        $this->pages[$pageNum]->fputcsv([$id, $parentId, $name]);
    }

    /**
     * @param int $id
     * @param int|null $parentId
     * @return void
     */
    private function addToIndex(int $id, ?int $parentId): void
    {
        $parentNum = $this->getPageIndex(($parentId ? : 0));
        if (!isset($this->parentPages[$parentNum])) {
            $this->parentPages[$parentNum] = new SplTempFileObject();
            $this->parentPages[$parentNum]->setFlags(SplFileObject::READ_CSV);
        }
        $this->parentPages[$parentNum]->fputcsv([$id]);
    }
}