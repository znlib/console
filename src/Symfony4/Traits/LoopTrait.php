<?php

namespace ZnLib\Console\Symfony4\Traits;

use React\EventLoop\Loop;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait LoopTrait
{

    private $loopInterval = 0;

//    abstract public function getInput(): InputInterface;

//    abstract public function getOutput(): OutputInterface;

    abstract protected function runLoopItem(): void;

    public function setLoopInterval(float $loopInterval): void
    {
        $this->loopInterval = $loopInterval;
    }

    protected function runProcess(): void
    {
        $this->runLoop();
    }

    protected function getLoopItemCallback(): callable
    {
        $callback = function () {
            $this->runLoopItem();
        };
        return $callback;
    }

    protected function runLoop()
    {
        $callback = $this->getLoopItemCallback();
        $loop = Loop::get();
        $loop->addPeriodicTimer($this->loopInterval, $callback);
        $loop->run();
    }
}
