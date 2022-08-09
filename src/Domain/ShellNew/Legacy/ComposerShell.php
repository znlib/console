<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use ZnLib\Console\Domain\ShellNew\BaseShellNew2;

class ComposerShell extends BaseShellNew2
{

    public function install(string $directory)
    {
        $command = "cd $directory && {{bin/composer}} install";
        return $this->shell->runCommand($command);
    }
}
