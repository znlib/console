<?php

namespace ZnLib\Console\Domain\Libs\BundleLoaders;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Libs\ConfigManager\Interfaces\ConfigManagerInterface;
use ZnCore\Base\Libs\App\Loaders\BundleLoaders\BaseLoader;

class ConsoleLoader extends BaseLoader
{

    public function loadAll(array $bundles): array
    {
        $config = [];
        foreach ($bundles as $bundle) {
            $loadedConfig = $this->load($bundle);
            $config = ArrayHelper::merge($config, $loadedConfig);
        }

        $configManager = $this->getContainer()->get(ConfigManagerInterface::class);
        $configManager->set('consoleCommands', $config);

        return $config ? ['consoleCommands' => $config] : [];
    }
}
