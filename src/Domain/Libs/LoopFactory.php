<?php

namespace ZnLib\Console\Domain\Libs;

use React\EventLoop\Loop;

class LoopFactory
{

    public static function createPeriodicTimer(float $interval = 0): array
    {
        $loop = Loop::get();
        set_exception_handler(function (\Throwable $e) use($loop) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
            $loop->stop();
        });
        $timer = $loop->addPeriodicTimer($interval, function () {
            //sleep(1);
            echo 'Tick' . PHP_EOL;
        });
        return $loop;
        $loop->run();
    }
}
