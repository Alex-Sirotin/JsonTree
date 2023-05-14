<?php

namespace ABCship\Application\Utils;

trait Memory
{
    public function getMemoryLimit(): int
    {
        $memoryLimit = ini_get('memory_limit');
        $size = [
            'k' => 1024,
            'm' => 1024 * 1024,
            'g' => 1024 * 1024 * 1024,
        ];

        $multiplier = strtolower(substr($memoryLimit, -1, 1));

        return ($size[$multiplier] ?? 1) * intval($memoryLimit);
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function isAvailable(int $offset = 0): bool
    {
        return $this->getMemory() < ($this->getMemoryLimit() - $offset);
    }

    /**
     * @param bool $realUsage
     * @return int
     */
    public function getMemory(bool $realUsage = true): int
    {
        return memory_get_usage($realUsage);
    }

    /**
     * @param int $limit
     * @param bool $realUsage
     * @return int
     */
    public function isLimitReached(int $limit, bool $realUsage = true): int
    {
        return $this->getMemory($realUsage) > $limit;
    }

    /**
     * @param int $before
     * @param bool $realUsage
     * @return int
     */
    public function getUsage(int $before, bool $realUsage = true): int
    {
        return memory_get_usage($realUsage) - $before;
    }

    /**
     * @param int $before
     * @param bool $realUsage
     * @return int
     */
    public function getPeakUsage(int $before, bool $realUsage = true): int
    {
        return memory_get_peak_usage($realUsage) - $before;
    }
}
