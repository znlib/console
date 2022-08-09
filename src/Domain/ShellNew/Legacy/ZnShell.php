<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use Deployer\ServerConsole;
use Deployer\Zn;
use ZnLib\Console\Domain\ShellNew\BaseShellNew2;

class ZnShell extends BaseShellNew2
{

    public function init(string $env) {
        return $this->runCommand("init --env=\"$env\" --overwrite=All", $env);
    }

    public function migrateUp(string $env = null) {
        return $this->runCommand("db:migrate:up --withConfirm=0", $env);
    }

    public function fixtureImport(string $env = null) {
        return $this->runCommand("db:fixture:import --withConfirm=0", $env);
    }

    public function runCommand(string $command, string $env = null) {
        $envCode = $env ? "--env=\"$env\"" : '';
        $this->cd('{{release_path}}/vendor/bin');
        return $this->runCommand("{{bin/php}} zn $command $envCode");
    }
}
