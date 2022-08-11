<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use Deployer\Zn;
use ZnLib\Console\Domain\ShellNew\BaseShellNew2;

class ZnShell extends BaseShellNew2
{

    public function init(string $env)
    {
        return $this->runZn("init --env=\"$env\" --overwrite=All", $env);
    }

    public function migrateUp(string $env = null)
    {
        return $this->runZn("db:migrate:up --withConfirm=0", $env);
    }

    public function fixtureImport(string $env = null)
    {
        return $this->runZn("db:fixture:import --withConfirm=0", $env);
    }

    public function runZn($command, ?string $path = null): string
    {
//        $envCode = $env ? "--env=\"$env\"" : '';
//        dd($envCode);
//        $this->cd('{{release_path}}/vendor/bin');
        return $this->runCommand("cd {{release_path}}/vendor/bin && {{bin/php}} zn $command");
    }
}
