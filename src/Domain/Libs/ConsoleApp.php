<?php

namespace ZnLib\Console\Domain\Libs;

use ZnLib\Console\Domain\Libs\BundleLoaders\ConsoleLoader;
use ZnLib\Console\Symfony4\Base\BaseConsoleApp;

class ConsoleApp extends BaseConsoleApp
{

    protected function initBundles(): void
    {
        $this->loadBundlesFromEnvPath();
        parent::initBundles();
    }
}
