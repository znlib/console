<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use Deployer\PhpConfig;
use Deployer\ServerFs;
use ZnLib\Console\Domain\ShellNew\BaseShellNew2;

class PhpShell extends BaseShellNew2
{

    public function setConfig(string $configFile, array $config) {
        $content = $this->fsClass()::downloadContent($configFile);
        $phpConfig = new PhpConfig($content);
        foreach ($config as $key => $value) {
            $phpConfig->enable($key);
            $phpConfig->set($key, $value);
        }
        $this->fsClass()::uploadContent($content, $configFile);
    }
}
