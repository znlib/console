<?php

use Illuminate\Container\Container;
use Symfony\Component\Console\Application;
use ZnCore\Base\App\Interfaces\AppInterface;
use ZnCore\Base\App\Libs\ZnCore;
use ZnCore\Base\Container\Interfaces\ContainerConfiguratorInterface;
use ZnCore\Base\DotEnv\Domain\Libs\DotEnv;
use ZnCore\Base\DotEnv\Domain\Libs\DotEnvLoader;
use ZnCore\Base\EventDispatcher\Interfaces\EventDispatcherConfiguratorInterface;
use ZnCore\Base\FileSystem\Helpers\FilePathHelper;
use ZnLib\Console\Domain\Libs\ConsoleApp;

return function (\Psr\Container\ContainerInterface $container) {

    /** @var ContainerConfiguratorInterface $containerConfigurator */
    $containerConfigurator = $container->get(ContainerConfiguratorInterface::class);

    /** @var EventDispatcherConfiguratorInterface $eventDispatcherConfigurator */
    $eventDispatcherConfigurator = $container->get(EventDispatcherConfiguratorInterface::class);

    //$mainEnv = DotEnv::loadFromFile(DotEnv::ROOT_PATH . '/.env');

    $loader = new DotEnvLoader();
    $mainEnv = $loader->loadFromFile(FilePathHelper::rootPath() . '/.env');
    $consoleAppClass = $mainEnv['CONSOLE_APP_CLASS'] ?? ConsoleApp::class;
    $containerConfigurator->singleton(AppInterface::class, $consoleAppClass);

    /** @var AppInterface $appFactory */
    $appFactory = $container->get(AppInterface::class);

    $appFactory->addBundles([
        \ZnTool\Package\Bundle::class,
        \ZnDatabase\Base\Bundle::class,
        \ZnDatabase\Tool\Bundle::class,
        \ZnDatabase\Fixture\Bundle::class,
        \ZnDatabase\Migration\Bundle::class,
        \ZnTool\Generator\Bundle::class,
        \ZnTool\Stress\Bundle::class,
        \ZnBundle\Queue\Bundle::class,
        \ZnCore\Base\DotEnv\Bundle::class,
    ]);
};
