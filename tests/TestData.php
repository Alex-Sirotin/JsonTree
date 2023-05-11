<?php

class TestData
{
    use \ABCShip\Util\Memory;

    const DEFAULT_ROW_LIMIT = 100_000;
    const DEFAULT_BATCH_SIZE = 10_000;
    const NODE_START_ID = 1;
    const NODE_STEP = 1;
    const NODE_FAKE_NAME = 'Node';

    private string $dir;
    private string $file;
    private string $fileName;
    private int $rowLimit;
    private Faker\Generator $faker;

    /**
     * @param int|null $rowLimit
     * @param string|null $dir
     * @param string|null $file
     */
    function __construct(?int $rowLimit = null, ?string $dir = null, ?string $file = null)
    {
        if (!$dir) {
            $this->dir = realpath(__DIR__ . "/../data");
        }

        $this->rowLimit = $rowLimit ?: self::DEFAULT_ROW_LIMIT;
        if (!$file) {
            $rowLimitFormatted = number_format($this->rowLimit, 0, '', '_');
            $this->file = "fake_data_{$rowLimitFormatted}.json";
        }
        $this->fileName = "{$this->dir}/{$this->file}";
    }

    /**
     * @param \Faker\Generator|null $faker
     * @return void
     */
    public function setFaker(?Faker\Generator $faker)
    {
        $this->faker = $faker;
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
            throw new \Exception ("Can't write to file '{$file->getPathname()}");
        }

        if ($this->rowLimit <= 0) {
            throw new \Exception ("RowLimit '{$this->rowLimit}' should be greater than 0");
        }

        return $file;
    }

    /**
     * @param int $id
     * @param int $startId
     * @param int $step
     * @param string|null $name
     * @return string
     */
    private function getElement(int $id, int $startId, int $step, ?string $name = null): string
    {
        if (!$name) {
            $name = self::NODE_FAKE_NAME . ' ' . $id;
            if (isset($this->faker)) {
                $name = $this->faker->word();
            }
        }

        return json_encode([
            'id' => $id,
            'name' => $name,
            'parent_id' => $this->calcParent($startId, $id, $step),
        ]);
    }

    /**
     * @param int $batchSize
     * @param int $startId
     * @param int $step
     * @param bool $recreateIfExists
     * @return string
     * @throws Exception
     */
    public function generateFile(
        int $batchSize = self::DEFAULT_BATCH_SIZE,
        int $startId = self::NODE_START_ID,
        int $step = self::NODE_STEP,
        bool $recreateIfExists = false
    ): string
    {
        $file = $this->checkFile($recreateIfExists);
        if (!$file) {
            return $this->fileName;
        }

        $file->fwrite("[\n");
        foreach($this->getData($batchSize, $startId, $step) as $batchId => $data) {
            $file->fwrite($data);
        }
        $file->fwrite("\n]");

        return $file->getPathname();
    }

    /**
     * @param int $batchSize
     * @param int $startId
     * @param int $step
     * @return Generator
     */
    public function getData(
        int $batchSize = self::DEFAULT_BATCH_SIZE,
        int $startId = self::NODE_START_ID,
        int $step = self::NODE_STEP
    ): Generator
    {
        //$result = [];
        $result = "";
        $batch = 0;
        $memoryLimit = intdiv($this->getMemoryLimit(), 2);

        $maxId = ($this->rowLimit-1) * $step + $startId;
        for ($i = $startId; $i <= $maxId; $i += $step) {

            $elem = $this->getElement($i, $startId, $step) . ",\n";
            $memoryUsed = (memory_get_usage() + strlen($elem));
            $isLimitReached = $memoryUsed > $memoryLimit;
            if ($isLimitReached || $i >= $maxId || $batch >= $batchSize) {
                yield $result;
                $result = "";
                $batch = 0;
            }
            $result .= $elem;
            $batch++;
        }
        if ($result) {
            yield trim($result, ",\n");
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
}