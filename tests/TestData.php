<?php

namespace ABCship\Application;

use Application\FileGenerator as FileGenerator;

class TestData
{
    /**
     * @param int $batchSize
     * @param int $startId
     * @param int $step
     * @param bool $recreateIfExists
     * @return string
     * @throws Exception
     */
    public function generateFile(
        int $batchSize = FileGenerator::DEFAULT_BATCH_SIZE,
        int $startId = FileGenerator::NODE_START_ID,
        int $step = FileGenerator::NODE_STEP,
        bool $recreateIfExists = false
    ): string
    {
        $file = new FileGenerator($rowLimit, $dir, $file);
        $file->generate($batchSize, $startId, $step, $recreateIfExists);
//        $file = $this->checkFile($recreateIfExists);
//        if (!$file) {
//            return $this->fileName;
//        }
//
//        $file->fwrite("[\n");
//        foreach($this->getData($batchSize, $startId, $step) as $batchId => $data) {
//            $file->fwrite($data);
//        }
//        $file->fwrite("\n]");
//
//        return $file->getPathname();
    }
}