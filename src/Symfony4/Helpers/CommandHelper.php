<?php

namespace ZnLib\Console\Symfony4\Helpers;

use Psr\Container\ContainerInterface;
use ReflectionException;
use Symfony\Component\Console\Application;
use ZnCore\Arr\Helpers\ArrayHelper;
use ZnCore\Code\Helpers\ComposerHelper;
use ZnCore\FileSystem\Helpers\FilePathHelper;
use ZnCore\FileSystem\Helpers\FindFileHelper;
use ZnCore\FileSystem\Helpers\FileHelper;

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

        if(is_dir($namespace)) {
            $path = $namespace;
        } else {
            $path = ComposerHelper::getPsr4Path($namespace);
        }

        $files = FindFileHelper::scanDir($path);
        $files = array_filter($files, function ($value) {
            return preg_match('/\.php$/i', $value);
        });

        $commands = array_map(function ($item) use ($namespace) {
            $cleanItem = str_replace('.php', '', $item);
            return $namespace . '\\' . $cleanItem;
        }, $files);

        foreach ($commands as $commandClassName) {

            if(is_dir($namespace)) {
                $classes = get_declared_classes();
                $file = realpath(FilePathHelper::up($namespace)) . '/' . basename($commandClassName) . '.php';
                $file = FileHelper::normalizePath($file);
                include $file;
                $loadedClasses = array_diff(get_declared_classes(), $classes);
                $commandClassName = ArrayHelper::first($loadedClasses);
            }

            $reflictionClass = new \ReflectionClass($commandClassName);
            if (!$reflictionClass->isAbstract()) {
                try {
                    $commandInstance = $container->get($commandClassName);
                    $application->add($commandInstance);
                } catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
                }
            }
        }
    }

}
