<?php

namespace ZnLib\Console\Symfony4\Helpers;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use ZnCore\Base\Helpers\ComposerHelper;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;

class CommandHelper
{

    public static function registerFromNamespaceList(array $namespaceList, ContainerInterface $container, Application $application = null)
    {
        foreach ($namespaceList as $namespace) {
            self::registerFromNamespace($namespace, $container, $application);
        }
    }

    public static function registerFromNamespace(string $namespace, ContainerInterface $container, Application $application = null)
    {
        if($application == null) {
            /** @var Application $application */
            $application = $container->get(Application::class);
        }

        $path = ComposerHelper::getPsr4Path($namespace);

        $files = FileHelper::scanDir($path);

        $commands = array_map(function ($item) use ($namespace) {
            $item = str_replace('.php', '', $item);
            return $namespace . '\\' . $item;
        }, $files);


        foreach ($commands as $commandClassName) {
            $reflictionClass = new \ReflectionClass($commandClassName);
            $isAbstract = $reflictionClass->isAbstract();
            if (!$isAbstract) {
                try {
                    $commandInstance = $container->get($commandClassName);
                    $application->add($commandInstance);
                } catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
                }
            }
        }
    }

}
