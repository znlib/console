<?php

namespace ZnLib\Console\Domain\Libs\BundleLoaders;

use ZnCore\Base\App\Loaders\BundleLoaders\BaseLoader;
use ZnCore\Base\Arr\Helpers\ArrayHelper;
use ZnCore\Base\ConfigManager\Interfaces\ConfigManagerInterface;

class ConsoleLoader extends BaseLoader
{

    public function loadAll(array $bundles): void
    {
        $config = [];
        foreach ($bundles as $bundle) {
            $loadedConfig = $this->load($bundle);
            $config = ArrayHelper::merge($config, $loadedConfig);
        }
        $configManager = $this->getContainer()->get(ConfigManagerInterface::class);
        $configManager->set('consoleCommands', $config);
    }
}
