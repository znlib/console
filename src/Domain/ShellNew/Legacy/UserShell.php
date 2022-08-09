<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use ZnLib\Console\Domain\ShellNew\BaseShellNew2;
use function Deployer\askHiddenResponse;

class UserShell extends BaseShellNew2
{

    public function setSudoPassword(): void
    {
        $pass = askHiddenResponse('Input sudo password:');
        $this->fsClass()::uploadContent($pass, '~/sudo-pass');
    }
}
