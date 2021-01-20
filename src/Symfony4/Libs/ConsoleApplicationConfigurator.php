<?php

namespace ZnLib\Console\Symfony4\Libs;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\Console\Application;
use ZnCore\Base\Helpers\ComposerHelper;
use ZnCore\Base\Helpers\EnvHelper;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnCore\Base\Libs\App\Base\BaseBundle;
use ZnCore\Base\Libs\App\Kernel;
use ZnCore\Base\Libs\App\Loaders\ContainerConfigLoader;
use ZnCore\Base\Libs\DotEnv\DotEnv;

class ConsoleApplicationConfigurator
{

    private $container;
    private $application;
    private $consoleCommandList;
    private $containerConfigLoader;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->containerConfigLoader = new ContainerConfigLoader();
    }

    public function loadConfig(Application $application)
    {
        DotEnv::init(__DIR__ . '/../../../../../..');
        EnvHelper::setErrorVisibleFromEnv();
        $kernel = new Kernel('console');
        $kernel->setContainer($this->container);
        $kernel->setLoader($this->containerConfigLoader);
        $mainConfig = $kernel->loadAppConfig();
        foreach ($this->consoleCommandList as $namespace) {
            $this->registerConsoleCommand($namespace, $application);
        }
    }

    public function registerContainerConfig(string $containerConfig)
    {
        $this->containerConfigLoader->addContainerConfig($containerConfig);
    }

    public function registerBundles(array $bundles)
    {
        foreach ($bundles as $bundle) {
            $this->registerBundle($bundle);
        }
    }

    public function registerBundle(BaseBundle $bundle)
    {
        $import = $bundle->getImportList();
        if (in_array('container', $import)) {
            $containerConfigList = $bundle->container();
            foreach ($containerConfigList as $config) {
                $this->containerConfigLoader->addContainerConfig($config);
            }
        }
        if (in_array('console', $import)) {
            $consoleCommands = $bundle->console();
            foreach ($consoleCommands as $namespace) {
                $this->consoleCommandList[] = $namespace;
            }
        }
    }

    public function registerConsoleCommand(string $namespace, Application $application)
    {
        $commands = $this->scanCommandsByNameSpace($namespace);
        foreach ($commands as $commandClassName) {
            $commandInstance = $this->getCommandInstance($commandClassName);
            if ($commandInstance) {
                $application->add($commandInstance);
            }
        }
    }

    private function getCommandInstance($commandClassName)
    {
        $reflictionClass = new ReflectionClass($commandClassName);
        $isAbstract = $reflictionClass->isAbstract();
        if ($isAbstract) {
            return null;
        }
        return $this->container->get($commandClassName);
    }

    private function scanCommandsByNameSpace(string $namespace): array
    {
        $path = ComposerHelper::getPsr4Path($namespace);
        $files = FileHelper::scanDir($path);
        $commands = array_map(function ($commandClassFile) use ($namespace) {
            $commandClassFile = str_replace('.php', '', $commandClassFile);
            return $namespace . '\\' . $commandClassFile;
        }, $files);
        return $commands;
    }
}
