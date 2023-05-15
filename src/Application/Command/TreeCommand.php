<?php

namespace ABCship\Application\Command;

use ABCship\JsonTree\DataLoader\JsonLoader;
use ABCship\JsonTree\StoreProvider\File\Provider;
use ABCship\JsonTree\Tree;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TreeCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('tree:json-load')
            ->addArgument('json', InputArgument::REQUIRED, 'Path to JSON file')
            ->addOption('memory', 'm', InputOption::VALUE_OPTIONAL, 'Memory limit for tree building')
            ->addOption('in-memory', 'i', InputOption::VALUE_NONE, 'Save tree to memory')
            ->addOption('db', 'b', InputOption::VALUE_NONE, 'Save tree to DB')
            ->addOption('redis', 'r', InputOption::VALUE_NONE, 'Save tree to Redis')
            ->addOption('file', 'f', InputOption::VALUE_NONE, 'Save tree to filesystem')
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'List all nodes')
            ->addOption('traverse', 't', InputOption::VALUE_NONE, 'Traverse tree')
            ->addOption('search', 's', InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY, 'Search node(s)')
            ->addOption('path', 'p', InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY, 'Show node(s) path');
        parent::configure();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $json = $input->getArgument('json');
        list(
            'memory' => $memoryLimit,
            'in-memory' => $inMemory,
            'db' => $db,
            'redis' => $redis,
            'file' => $file,
            'list' => $list,
            'search' => $search,
            'path' => $path,
            'traverse' => $traverse
        ) = $input->getOptions();

        if ($inMemory || $db || $redis) {
            $output->writeln('Not implemented yet!');
            return Command::SUCCESS;
        }

        if (!($inMemory || $db || $redis || $file)) {
            $output->writeln('Store provider options (file|db|redis|in-memory) not set');
            return Command::FAILURE;
        }

        $provider = null;
        if ($file) {
            $provider = new Provider($memoryLimit);
        }

        $loader = new JsonLoader();
        $tree = new Tree($provider);
        $tree->buildTree($loader->loadFromFile($json));

        if ($list) {
            $output->writeln('List nodes');
            foreach ($tree->flattenTree() as $node) {
                list('id' => $id, 'name' => $name, 'parent_id' => $parent) = $node;
                $output->writeln("ID: {$id}, ParentID: {$parent}, name: {$name}");
            }
            $output->writeln('');
        }

        if ($search) {
            $output->writeln('Search nodes');
            foreach ($search as $nodeId) {
                $output->writeln("Node: {$nodeId}");
                $found = $tree->search($nodeId);
                if ($found) {
                    $output->writeln((string)$found);
                } else {
                    $output->writeln("Not found");
                }
            }
            $output->writeln('');
        }

        if ($path) {
            $output->writeln('Path to nodes');
            foreach ($path as $nodeId) {
                $output->writeln("Node: {$nodeId}");
                foreach ($tree->searchTree($nodeId) as $nodePath) {
                    $output->writeln($nodePath);
                }
                $output->writeln('');
            }
            $output->writeln('');
        }

        if ($traverse) {
            $output->writeln('Traverse');
            $tree->traverseDepthFirst(function($node) use ($output) {
                $output->writeln("I'm {$node->getName()}({$node->getId()})");
            });
            $output->writeln('');
        }

        return Command::SUCCESS;
    }
}
