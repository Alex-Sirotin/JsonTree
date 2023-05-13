<?php

namespace ABCShip\Util;

trait Memory
{
    function getMemoryLimit()
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

    function isAvailable(int $offset): bool
    {
        $memoryLimit = intdiv($this->getMemoryLimit(), 2);
        $memoryUsed = (memory_get_usage() + $offset);

        return $memoryUsed < $memoryLimit;
    }

    public function getMemory(): int
    {
        return memory_get_usage();
    }

    public function getUsage(int $before): int
    {
        return memory_get_usage() - $before;
    }
}