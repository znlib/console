<?php

namespace ZnLib\Console\Domain\Libs;

use Symfony\Component\Process\Process;
use ZnCore\Base\Libs\FileSystem\Helpers\FilePathHelper;
use ZnLib\Console\Domain\Helpers\CommandLineHelper;

class ZnShell
{

    public function runProcessFromCommandString(string $command): Process
    {
        $process = $this->createProcess($command);
        $process->run();
        return $process;
    }

    public function createProcess(string $command): Process
    {
        $path = FilePathHelper::rootPath() . '/vendor/zncore/base/bin';
        /*$commandString = CommandLineHelper::argsToString([
            'php',
            'zn',
        ]);*/
        $commandString = "php ";
        $process = Process::fromShellCommandline($commandString . ' ' . $command, $path);
        return $process;
    }
}
