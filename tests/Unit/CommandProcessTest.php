<?php

namespace ZnLib\Console\Tests\Unit;

use Symfony\Component\Process\Process;
use ZnLib\Console\Symfony4\Helpers\CommandLineHelper;
use ZnLib\Console\Symfony4\Shell\FileSystemShell;
use ZnTool\Test\Base\BaseTest;

final class CommandProcessTest extends BaseTest
{

    public function testFileShell()
    {
        $path = realpath(__DIR__ . '/../example/directory');
//        $path = realpath( '/etc');
        $fss = new FileSystemShell();
        $list = $fss->directoryFiles($path);
//        dd($list);

        $expected = [
            [
//        "permission" => "drwxrwxr-x",
                "type" => "dir",
//    "user" => "vitaliy",
//    "group" => "vitaliy",
                "size" => 4096,
//    "month" => "Jun",
//    "day" => "9",
//    "time" => "14:27",
                "fileName" => "dir1",
            ],
            [
//        "permission" => "-rw-rw-r--",
                "type" => "file",
//    "user" => "vitaliy",
//    "group" => "vitaliy",
                "size" => 10,
//    "month" => "Jun",
//    "day" => "9",
//    "time" => "13:50",
                "fileName" => "file1.txt",
            ],
            [
//        "permission" => "-rw-rw-r--",
                "type" => "file",
//    "user" => "vitaliy",
//    "group" => "vitaliy",
                "size" => 20,
//    "month" => "Jun",
//    "day" => "9",
//    "time" => "13:50",
                "fileName" => "file2.txt",
            ],
            [
//        "permission" => "-rw-rw-r--",
                "type" => "file",
//    "user" => "vitaliy",
//    "group" => "vitaliy",
                "size" => 28,
//    "month" => "Jun",
//    "day" => "9",
//    "time" => "13:50",
                "fileName" => "file3.txt",
            ],
        ];
        $this->assertArraySubset($expected, $list);
    }

    public function ____testExecute()
    {
        $path = realpath(__DIR__ . '/../example/directory');
//        dd($path);

        $commandString = CommandLineHelper::argsToString(['ls', '-l']);
        $process = Process::fromShellCommandline($commandString, $path);
        $process->run();
        $commandOutput = $process->getOutput();
//        die($commandOutput);

        $expected = "итого 4
drwxrwxr-x 2 vitaliy vitaliy 4096 июн  9 14:27 dir1
-rw-rw-r-- 1 vitaliy vitaliy    0 июн  9 13:50 file1.txt
-rw-rw-r-- 1 vitaliy vitaliy    0 июн  9 13:50 file2.txt
-rw-rw-r-- 1 vitaliy vitaliy    0 июн  9 13:50 file3.txt
";
        $this->assertEquals($expected, $commandOutput);


        $commandString = CommandLineHelper::argsToString(['ls', '-l'], 'en_GB');
        $process = Process::fromShellCommandline($commandString, $path);
        $process->run();
        $commandOutput = $process->getOutput();
//        die($commandOutput);

        $expected = "total 4
        
-rw-rw-r-- 1 vitaliy vitaliy 0 Jun  9 13:50 file1.txt
-rw-rw-r-- 1 vitaliy vitaliy 0 Jun  9 13:50 file2.txt
-rw-rw-r-- 1 vitaliy vitaliy 0 Jun  9 13:50 file3.txt
";
        $this->assertEquals($expected, $commandOutput);
    }

}