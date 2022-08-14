<?php

use Psr\Container\ContainerInterface;
use ZnCore\App\Interfaces\AppInterface;
use ZnCore\Container\Interfaces\ContainerConfiguratorInterface;
use ZnCore\DotEnv\Domain\Libs\DotEnvLoader;
use ZnCore\EventDispatcher\Interfaces\EventDispatcherConfiguratorInterface;
use ZnLib\Console\Domain\Libs\ConsoleApp;
use ZnTool\Dev\VarDumper\Subscribers\SymfonyDumperSubscriber;

return function (ContainerInterface $container) {

    /** @var ContainerConfiguratorInterface $containerConfigurator */
    $containerConfigurator = $container->get(ContainerConfiguratorInterface::class);

    /** @var EventDispatcherConfiguratorInterface $eventDispatcherConfigurator */
    $eventDispatcherConfigurator = $container->get(EventDispatcherConfiguratorInterface::class);

    $loader = new DotEnvLoader();
    $mainEnv = $loader->loadFromFile(__DIR__ . '/../../../../.env');
    $consoleAppClass = $mainEnv['CONSOLE_APP_CLASS'] ?? ConsoleApp::class;
    $containerConfigurator->singleton(AppInterface::class, $consoleAppClass);

    /** @var AppInterface $appFactory */
    $appFactory = $container->get(AppInterface::class);


    if(class_exists(SymfonyDumperSubscriber::class)) {
        /** Подключаем дампер */
        $eventDispatcherConfigurator->addSubscriber(SymfonyDumperSubscriber::class);
    }

    $appFactory->addBundles([
        \ZnTool\Package\Bundle::class,
        \ZnDatabase\Base\Bundle::class,
        \ZnDatabase\Tool\Bundle::class,
        \ZnDatabase\Fixture\Bundle::class,
        \ZnDatabase\Migration\Bundle::class,
        \ZnTool\Generator\Bundle::class,
        \ZnTool\Stress\Bundle::class,
        \ZnBundle\Queue\Bundle::class,
        \ZnCore\DotEnv\Bundle::class,
    ]);
};
