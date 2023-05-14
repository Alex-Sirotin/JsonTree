<?php

namespace ABCship\Application\Command;

use ABCship\Application\FileGenerator;
use ABCship\Application\Utils\Memory;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    use Memory;

    protected function configure(): void
    {
        parent::configure();

        $dir = realpath(APPLICATION_PATH . FileGenerator::DEFAULT_DIR);
        $filename = realpath(FileGenerator::getDefaultFileName());

        $this->setName('tree:file-generate')
            ->addArgument('filename', InputArgument::OPTIONAL, 'Directory to store generated files', $filename)
            ->addArgument('dir', InputArgument::OPTIONAL, 'Directory to store generated files', $dir)
            ->addOption('rows', 'r', InputOption::VALUE_OPTIONAL, 'Set rows count', FileGenerator::DEFAULT_ROW_LIMIT)
            ->addOption('batch', 'b', InputOption::VALUE_OPTIONAL, 'Set batch size to adjust memory/performance', FileGenerator::DEFAULT_BATCH_SIZE)
            ->addOption('start-node', 's', InputOption::VALUE_OPTIONAL, 'Node start ID', FileGenerator::NODE_START_ID)
            ->addOption('step', 't', InputOption::VALUE_OPTIONAL, 'Step between node IDs', FileGenerator::NODE_STEP)
            ->addOption('label', 'l', InputOption::VALUE_OPTIONAL, 'Node name prefix', FileGenerator::NODE_FAKE_NAME)
            ->addOption('generate-name', 'g', InputOption::VALUE_NONE, 'Generate name instead of default')
            ->addOption('recreate', 'x', InputOption::VALUE_NONE, 'Recreate file if exists')
            ->addOption('memory', 'm', InputOption::VALUE_OPTIONAL, 'Memory limit for tree building');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = $input->getOptions();
        $rows = (int)$input->getOption('rows');
        if ($rows < 1) {
            $output->writeln("Row count should be greater than 0");
            return Command::FAILURE;
        }

        $batchSize = (int)$input->getOption('batch');
        if ($batchSize < 1) {
            $output->writeln("Batch size should be greater than 0");
            return Command::FAILURE;
        }

        $startId = (int)$input->getOption('start-node');
        $step = (int)$input->getOption('step');
        if ($step < 1) {
            $output->writeln("Step should be greater than 0");
            return Command::FAILURE;
        }

        $dir = $input->getArgument('dir');
        $file = $input->getArgument('filename');

        $memory = (int)$input->getOption('memory');
        if (($options['memory'] ?? false) && $memory < 1) {
            $output->writeln("Memory limit should be greater than 0");
            return Command::FAILURE;
        }

        $useFaker = $input->getOption('generate-name');
        $name = $input->getOption('label');
        $recreateIfExists = $input->getOption('recreate');

        $timeStart = hrtime(true);
        $memoryStart = $this->getMemory();

        $fileName = FileGenerator::generateFile(
            $rows,
            $dir,
            $file,
            $memory,
            $batchSize,
            $startId,
            $step,
            $useFaker,
            $name,
            $recreateIfExists
        );

        $usedMemory = round(($this->getPeakUsage($memoryStart)) / 1024 / 1024, 3);
        $timeEnd = round((hrtime(true) - $timeStart) / 1024 / 1024 / 1024, 3);

        $output->writeln('');
        $output->writeln("Generated file: {$fileName}");
        $output->writeln("Time: {$timeEnd} s");
        $output->writeln("Memory: {$usedMemory} Mb");

        return Command::SUCCESS;
    }
}
