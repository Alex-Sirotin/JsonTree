<?php

include 'Bootstrap.php';

//use ABCShip\JsonTree\Tree;
//
//$tree = new Tree();
//
//$tree->buildTree();
class Test
{
    use \ABCShip\Util\Memory;

    public function testMemory()
    {
        $begin = hrtime(true);
        $before = $this->getMemory();
//        $fileName = tempnam(sys_get_temp_dir(), 'memtest');
//        $file = new SplFileObject($fileName, 'w');
        $file = new SplTempFileObject();
        $created = $this->getUsage($before);

        for ($i = 1; $i < 10_000_000; $i++) {
            $file->fputcsv([$i, $i, $i]);
        }

        $written = $this->getUsage($before);
        echo json_encode([
                'time' => round((hrtime(true) - $begin)/1000/1000/1000, 3),
                'before' => $before,
                'created' => $created,
                'written' => $written,
                'count' => intdiv($this->getMemoryLimit(), $written),
            ]) . PHP_EOL;
    }
}

(new Test())->testMemory();