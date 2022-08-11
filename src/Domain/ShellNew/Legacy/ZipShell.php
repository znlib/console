<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use ZnLib\Console\Domain\ShellNew\BaseShellNew2;

class ZipShell extends BaseShellNew2
{

    private $directory;

    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    public function unZipAllToDir(string $zipFile, string $targetDirectory)
    {
        $this->shell->runCommand("cd \"{$targetDirectory}\" && unzip -o \"{$zipFile}\"");
    }
}
