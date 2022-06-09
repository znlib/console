<?php

namespace ZnLib\Console\Domain\Base;

use Symfony\Component\Process\Process;
use ZnLib\Console\Domain\Helpers\CommandLineHelper;

abstract class BaseShellNew
{

    protected $lang = 'en_GB';
    protected $path = null;

    protected function runCommand($command, ?string $path = null): string
    {
        $path = $path OR $this->path;
        $commandString = CommandLineHelper::argsToString($command, $this->lang);
        $process = Process::fromShellCommandline($commandString, $path);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \Exception('Console command error: ' . $process->getOutput());
        }
        $commandOutput = $process->getOutput();
        return $commandOutput;
    }
}
