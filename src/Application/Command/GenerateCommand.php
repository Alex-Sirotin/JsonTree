<?php

namespace ABCship\Application\Command;

use ABCship\Application\FileGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this->setName('tree:file-generate')
            ->addOption('rows', 'r', InputOption::VALUE_OPTIONAL, 'Set rows count', FileGenerator::DEFAULT_ROW_LIMIT)
            ->addOption('batch', 'b', InputOption::VALUE_OPTIONAL, 'Set batch size to adjust memory/performance', FileGenerator::DEFAULT_BATCH_SIZE)
            ->addOption('start-node', 's', InputOption::VALUE_OPTIONAL, 'Node start ID', FileGenerator::NODE_START_ID)
            ->addOption('step', 't', InputOption::VALUE_OPTIONAL, 'Step between node IDs', FileGenerator::NODE_STEP)
            ->addOption('name', 'n', InputOption::VALUE_OPTIONAL, 'Node name prefix', FileGenerator::NODE_FAKE_NAME)
            ->addOption('memory', 'm', InputOption::VALUE_OPTIONAL, 'Memory limit for tree building');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        error_reporting(0);
        $rows = (int)$input->getOption('rows');
        if ($rows > 0) {
            $this->generate($rows);
        }
        //        if ($input->getOption("xlswriter") || $all) {
        //            $this->runGenerator(new PhpXlsWriterGenerator($output), 'PhpXlsWriter.xlsx', $output);
        //        }


        $this->printResult($output);

        return Command::SUCCESS;
    }

    private function generate(int $rows)
    {
        $file = new FileGenerator($rows, $dir, $file);
        $file->generate($batchSize, $startId, $step, $recreateIfExists);
    }

    protected function printResult(OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln('Result');

        //        $table = new Table($output);
        //        $header       = ['ID', 'Library', 'Time, ms', 'Rows', 'Columns'];
        //        $table->setHeaders($header);
        //        $i = 1;
        //        foreach ($this->times as $name => $value) {
        //            $table->addRow([
        //                $i++,
        //                $name,
        //                $value,
        //                $this->rowsCount,
        //                $this->cellRepeat * 6
        //            ]);
        //        }

        //        $table->render();
        $output->writeln('');
    }
}
