<?php

namespace ABCship\Application;

use ABCship\Application\Utils\Memory;
use Exception;
use \Faker\Factory as FakerFactory;
use \Faker\Generator as FakeGenerator;
use Generator;
use SplFileInfo;
use SplFileObject;

class FileGenerator
{
    use Memory;

    public const DEFAULT_DIR = APPLICATION_PATH . "/data";
    public const DEFAULT_ROW_LIMIT = 100_000;
    public const DEFAULT_BATCH_SIZE = 10_000;
    public const NODE_START_ID = 1;
    public const NODE_STEP = 1;
    public const NODE_FAKE_NAME = 'Node';

    private string $dir;
    private string $file;
    private string $fileName;
    private int $rowLimit;
    private int $memoryLimit;
    private FakeGenerator $faker;

    /**
     * @param int|null $rowLimit
     * @param string|null $dir
     * @param string|null $file
     * @param int|null $memoryLimit
     * @throws Exception
     */
    public function __construct(
        ?int $rowLimit = null,
        ?string $dir = null,
        ?string $file = null,
        ?int $memoryLimit = 0
    ) {
        $this->dir = realpath(self::DEFAULT_DIR);
        if ($dir) {
            $this->dir = $dir;
        }
        if (!is_dir($this->dir)) {
            throw new Exception("Directory {$this->dir} not exists!");
        }

        $this->rowLimit = $rowLimit ?: self::DEFAULT_ROW_LIMIT;
        $this->file = $this->getDefaultFileName($this->rowLimit);

        if ($file) {
            $this->file = $file;
        }

        $this->fileName = "{$this->dir}/{$this->file}";

        if (file_exists($this->fileName) && !is_writable($this->fileName)) {
            throw new Exception("Can't write file {$this->fileName}");
        }

        $this->faker = FakerFactory::create();

        $this->memoryLimit = $memoryLimit && ($memoryLimit < $this->getMemoryLimit()) ? $memoryLimit : intdiv($this->getMemoryLimit(), 2);
    }

    /**
     * @param int|null $rowLimit
     * @return string
     */
    public static function getDefaultFileName(?int $rowLimit = null): string
    {
        $rowLimit = $rowLimit ?: self::DEFAULT_ROW_LIMIT;
        $rowLimitFormatted = number_format($rowLimit, 0, '', '_');
        return "fake_data_{$rowLimitFormatted}.json";
    }

    /**
     * @param bool $recreateIfExists
     * @return SplFileObject|null
     * @throws Exception
     */
    private function checkFile(bool $recreateIfExists = false): ?SplFileObject
    {
        $fileInfo = new SplFileInfo($this->fileName);

        if (!$recreateIfExists && $fileInfo->isFile()) {
            return null;
        }

        $file = new SplFileObject($fileInfo->getPathname(), "w");

        if (!$file->isWritable()) {
            throw new Exception("Can't write to file '{$file->getPathname()}");
        }

        if ($this->rowLimit <= 0) {
            throw new Exception("RowLimit '{$this->rowLimit}' should be greater than 0");
        }

        return $file;
    }

    /**
     * @param int $nodeId
     * @param int $startId
     * @param int $step
     * @param bool $useFaker
     * @param string|null $name
     * @return string
     */
    private function getElement(int $nodeId, int $startId, int $step, bool $useFaker = false, ?string $name = null): string
    {
        if (!$name) {
            $name = self::NODE_FAKE_NAME;
        }
        $name .= ' ' . $nodeId;

        if (isset($this->faker) && $useFaker) {
            $name = $this->faker->word();
        }

        return json_encode([
            'id' => $nodeId,
            'name' => $name,
            'parent_id' => $this->calcParent($startId, $nodeId, $step),
        ]);
    }

    /**
     * @param int $batchSize
     * @param int $startId
     * @param int $step
     * @param bool $recreateIfExists
     * @param bool $useFaker
     * @param string|null $name
     * @return string
     * @throws Exception
     */
    public function generate(
        int $batchSize = self::DEFAULT_BATCH_SIZE,
        int $startId = self::NODE_START_ID,
        int $step = self::NODE_STEP,
        bool $recreateIfExists = false,
        bool $useFaker = false,
        ?string $name = null
    ): string {
        $file = $this->checkFile($recreateIfExists);
        if (!$file) {
            return $this->fileName;
        }

        $file->fwrite("[" . PHP_EOL);
        foreach ($this->getData($batchSize, $startId, $step, $useFaker, $name) as $data) {
            $file->fwrite($data);
        }
        $file->fwrite(PHP_EOL . "]");

        return $file->getPathname();
    }

    /**
     * @param int $batchSize
     * @param int $startId
     * @param int $step
     * @param bool $useFaker
     * @param string|null $name
     * @return Generator
     */
    private function getData(
        int $batchSize = self::DEFAULT_BATCH_SIZE,
        int $startId = self::NODE_START_ID,
        int $step = self::NODE_STEP,
        bool $useFaker = false,
        ?string $name = null
    ): Generator {
        $result = "";
        $batch = 0;
        $maxId = ($this->rowLimit - 1) * $step + $startId;
        for ($i = $startId; $i <= $maxId; $i += $step) {
            $isLimitReached = $this->isLimitReached($this->memoryLimit - strlen($result));
            if ($isLimitReached || $i >= $maxId || $batch >= $batchSize) {
                yield $result;
                $result = "";
                $batch = 0;
            }
            $result .= $this->getElement($i, $startId, $step, $useFaker, $name) . "," . PHP_EOL;
            $batch++;
        }

        if ($result) {
            yield trim($result, "," . PHP_EOL);
            unset($result);
        }
    }

    /**
     * @param int $start
     * @param int $end
     * @param int $step
     * @return int|null
     */
    private function calcParent(int $start, int $end, int $step): ?int
    {
        if ($end <= $start) {
            return null;
        }

        do {
            $result = rand($start, $end - $step);
        } while ((($result - $start) % $step) !== 0);

        return $result;
    }

    /**
     * @throws Exception
     */
    public static function generateFile(
        ?int $rows = null,
        ?string $dir = null,
        ?string $file = null,
        ?int $memoryLimit = null,
        ?int $batchSize = self::DEFAULT_BATCH_SIZE,
        ?int $startId = self::NODE_START_ID,
        ?int $step = self::NODE_STEP,
        ?bool $useFaker = false,
        ?string $label = null,
        ?bool $recreateIfExists = false
    ): string {
        $file = new self($rows, $dir, $file, $memoryLimit);
        return $file->generate($batchSize, $startId, $step, $recreateIfExists, $useFaker, $label);
    }
}
