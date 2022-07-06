<?php

namespace ZnLib\Console\Domain\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;
use ZnCore\App\Enums\AppEventEnum;
use ZnLib\Console\Domain\Libs\EnvDetector\ConsoleEnvDetector;

class ConsoleDetectTestEnvSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            AppEventEnum::BEFORE_INIT_ENV => 'onBeforeInitEnv',
        ];
    }

    public function onBeforeInitEnv(Event $event)
    {
        $envDetector = new ConsoleEnvDetector();
        $isTest = $envDetector->isTest();
        if ($isTest) {
            $_ENV['APP_ENV'] = 'test';
        }
        $_ENV['APP_MODE'] = $isTest ? 'test' : 'main';
    }
}
