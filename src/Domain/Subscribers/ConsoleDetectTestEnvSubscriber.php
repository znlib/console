<?php

namespace ZnLib\Console\Domain\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;
use ZnCore\Base\Libs\App\Enums\AppEventEnum;

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
        //$envDetector = new \ZnCore\Base\Libs\App\Libs\EnvDetector\WebEnvDetector();
$envDetector = new \ZnLib\Console\Domain\Libs\EnvDetector\ConsoleEnvDetector();
//        $envDetector = new \ZnCore\Base\Libs\App\Libs\EnvDetector\EnvDetector();
        $isTest = $envDetector->isTest();
//        global $_GET, $_SERVER;
//        $isTest = (isset($_SERVER['HTTP_ENV_NAME']) && $_SERVER['HTTP_ENV_NAME'] == 'test') || (isset($_GET['env']) && $_GET['env'] == 'test');
        if ($isTest) {
            $_ENV['APP_ENV'] = 'test';
        }
        $_ENV['APP_MODE'] = $isTest ? 'test' : 'main';





        /*global $argv;
        $isConsoleTest = isset($argv) && in_array('--env=test', $argv);
        if ($isConsoleTest) {
            $_ENV['APP_ENV'] = 'test';
        }*/
    }
}
