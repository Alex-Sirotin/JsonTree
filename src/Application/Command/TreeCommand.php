<?php

namespace ABCship\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//    use Symfony\Component\Console\Helper\Table;

class TreeCommand extends Command
{
    //    private $rowsCount = 1000;
    //    private $cellRepeat = 1;
    //    private $times = [];
    //    private $timestamp;
    //    private $uniqid;

    protected function configure()
    {
        $this->setName('tree:json-load')
            ->addOption('source', 's', InputOption::VALUE_REQUIRED, 'Path to JSON file')
            ->addOption('memory', 'm', InputOption::VALUE_OPTIONAL, 'Memory limit for tree building')
            ->addOption('in-memory', 'i', InputOption::VALUE_OPTIONAL, 'Save tree to memory')
            ->addOption('db', 'b', InputOption::VALUE_OPTIONAL, 'Save tree to DB')
            ->addOption('redis', 'r', InputOption::VALUE_OPTIONAL, 'Save tree to Redis')
            ->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Save tree to filesystem')
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Dry run, data loading only');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        error_reporting(0);
//        $rows = (int)$input->getOption('generate');
//        if (is_numeric($rows) && $rows > 0) {
//            $this->generate($rows);
//        }
        //        $repeats = (int)$input->getOption('cellrepeat');
        //        if (is_numeric($repeats) && $repeats > 0) {
        //            $this->cellRepeat = $repeats;
        //        }
        //        $all = $input->getOption('all');
        //
        //        $output->writeln('Generating...');
        //        $columns = $this->cellRepeat * 6;
        //        $output->writeln("Rows: {$this->rowsCount}, Columns: {$columns}");
        //        $output->writeln('');
        //        $this->timestamp = time();
        //        $this->uniqid = uniqid();
        //        if ($input->getOption("simplexlsgen") || $all) {
        //            $this->runGenerator(new SimpleXlsxGenerator($output), 'SimpleXlsxGen.xlsx', $output);
        //        }
        //        if ($input->getOption("onesheet")) {
        //            $this->runGenerator(new OneSheetGenerator($output), 'OneSheet.xlsx', $output);
        //        }
        //        if ($input->getOption("ellumilelphpexcelwriter") || $all) {
        //            $this->runGenerator(
        //                new EllumilelPhpExcelWriterGenerator($output),
        //                'EllumilelPhpExcelWriter.xlsx',
        //                $output
        //            );
        //        }
        //        if ($input->getOption("xlswriter") || $all) {
        //            $this->runGenerator(new PhpXlsWriterGenerator($output), 'PhpXlsWriter.xlsx', $output);
        //        }


        $this->printResult($output);

        return Command::SUCCESS;
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

//    protected function runGenerator(FileGenerator $generator, string $filename, OutputInterface $output)
//    {
//        $output->writeln($filename);
//        $startTime = microtime(true);
//        $fullName = "{$this->uniqid}_{$this->timestamp}_{$this->rowsCount}_{$this->cellRepeat}_{$filename}";
//        $generator->setFilename($fullName);
//        $generator->generate($this->rowsCount, $this->cellRepeat);
//        $generator->save("output/{$fullName}");
//        $this->times[$filename] = microtime(true) - $startTime;
//        $output->writeln('');
//        $output->writeln('');
//    }
}
