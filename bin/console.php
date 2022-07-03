<?php

use ZnCore\Base\Container\Libs\Container;
use Symfony\Component\Console\Application;
use ZnCore\Base\App\Interfaces\AppInterface;
use ZnCore\Base\App\Libs\ZnCore;
use ZnCore\Base\Container\Interfaces\ContainerConfiguratorInterface;
use ZnCore\Base\EventDispatcher\Interfaces\EventDispatcherConfiguratorInterface;
use ZnLib\Init\Helpers\InitHelper;

define('MICRO_TIME', microtime(true));

require __DIR__ . '/../../../../vendor/autoload.php';

$container = new Container();
$znCore = new ZnCore($container);
$znCore->init();

/** @var ContainerConfiguratorInterface $containerConfigurator */
$containerConfigurator = $container->get(ContainerConfiguratorInterface::class);

/** @var EventDispatcherConfiguratorInterface $eventDispatcherConfigurator */
$eventDispatcherConfigurator = $container->get(EventDispatcherConfiguratorInterface::class);

//$isInit = preg_match('/^init:/', $argv[1]);
$isInit = class_exists(InitHelper::class) && InitHelper::isInitCommand($argv[1]);
if ($isInit) {
    $bootstrap = require __DIR__ . '/../../init/bin/bootstrap.php';
} else {
    $bootstrap = require __DIR__ . '/bootstrap.php';
}

call_user_func($bootstrap, $container);

/** @var AppInterface $appFactory */
$appFactory = $container->get(AppInterface::class);
$appFactory->init();

/** @var Application $application */
$application = $container->get(Application::class);
$application->run();
