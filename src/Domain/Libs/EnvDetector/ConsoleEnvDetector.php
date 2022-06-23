<?php

namespace ZnLib\Console\Domain\Libs\EnvDetector;

use ZnCore\Base\App\Interfaces\EnvDetectorInterface;

class ConsoleEnvDetector implements EnvDetectorInterface
{

    public function isMatch(): bool
    {
        return php_sapi_name() == 'cli';

//        global $argv;
//        return isset($argv);
    }

    public function isTest(): bool
    {
        global $argv;
        $isConsoleTest = in_array('--env=test', $argv);
        return $isConsoleTest;
    }
}
