<?php

namespace ZnLib\Console\Domain\Libs;

use ZnCore\DotEnv\Domain\Libs\DotEnv;
use ZnLib\Console\Symfony4\Base\BaseConsoleApp;

class ConsoleApp extends BaseConsoleApp
{

    protected function initBundles(): void
    {
        $this->loadBundlesFromEnvPath();
        parent::initBundles();
    }

    protected function loadBundlesFromEnvPath(): void
    {
        $bundles = [];
        if (DotEnv::get('BUNDLES_CONFIG_FILE')) {
            $bundles = include __DIR__ . '/../../../../../../' . DotEnv::get('BUNDLES_CONFIG_FILE');
        }
        $this->addBundles($bundles);
    }
}
