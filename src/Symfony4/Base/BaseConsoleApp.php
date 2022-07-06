<?php

namespace ZnLib\Console\Symfony4\Base;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\Base\ConfigManager\Interfaces\ConfigManagerInterface;
use ZnCore\Container\Interfaces\ContainerConfiguratorInterface;
use ZnCore\EventDispatcher\Interfaces\EventDispatcherConfiguratorInterface;
use ZnLib\Console\Domain\Libs\BundleLoaders\ConsoleLoader;
use ZnLib\Console\Symfony4\Helpers\CommandHelper;
use ZnLib\Rpc\Domain\Subscribers\ApplicationAuthenticationSubscriber;
use ZnCore\App\Base\BaseApp;
use ZnCore\App\Libs\ZnCore;
use ZnLib\Console\Domain\Subscribers\ConsoleDetectTestEnvSubscriber;

abstract class BaseConsoleApp extends BaseApp
{

    private $configManager;

    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $dispatcher,
        ZnCore $znCore,
        ContainerConfiguratorInterface $containerConfigurator,
        ConfigManagerInterface $configManager
    )
    {
        parent::__construct($container, $dispatcher, $znCore, $containerConfigurator);
        $this->configManager = $configManager;
    }

    public function appName(): string
    {
        return 'console';
    }

    public function subscribes(): array
    {
        return [
            ConsoleDetectTestEnvSubscriber::class,
        ];
    }

    public function import(): array
    {
        return ['i18next', 'container', 'rbac', 'console', 'migration', 'symfonyRpc', 'telegramRoutes'];
    }

    protected function bundleLoaders(): array
    {
        $loaders = parent::bundleLoaders();
        $loaders['console'] = ConsoleLoader::class;
        return $loaders;
    }

    public function init(): void
    {
        parent::init();
        $consoleCommands = $this->configManager->get('consoleCommands');
        $this->createConsole($consoleCommands);
    }

    protected function configContainer(ContainerConfiguratorInterface $containerConfigurator): void
    {
        $containerConfigurator->singleton(Application::class, Application::class);
    }

    protected function configDispatcher(EventDispatcherConfiguratorInterface $configurator): void
    {

    }

    protected function createConsole(array $consoleCommands)
    {
        $container = $this->getContainer();

        /** @var Application $application */
        $application = $container->get(Application::class);
        $application->getDefinition()->addOptions([
            new InputOption(
                '--env',
                '-e',
                InputOption::VALUE_OPTIONAL,
                'The environment to operate in.',
                'DEV'
            )
        ]);
        if (!empty($consoleCommands)) {
            CommandHelper::registerFromNamespaceList($consoleCommands, $container, $application);
        }
    }
}
