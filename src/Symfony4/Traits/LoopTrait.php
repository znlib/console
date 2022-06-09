<?php

namespace ZnLib\Console\Symfony4\Traits;

use React\EventLoop\Loop;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait LoopTrait
{

    private $loopInterval = 0;

    abstract protected function runLoopItem(InputInterface $input, OutputInterface $output): void;

    public function setLoopInterval(float $loopInterval): void
    {
        $this->loopInterval = $loopInterval;
    }

    protected function runProcess(InputInterface $input, OutputInterface $output): void
    {
        $this->runLoop($input, $output);
    }

    protected function getLoopItemCallback(InputInterface $input, OutputInterface $output): callable
    {
        $callback = function () use ($input, $output) {
            $this->runLoopItem($input, $output);
        };
        return $callback;
    }

    protected function runLoop(InputInterface $input, OutputInterface $output)
    {
        $callback = $this->getLoopItemCallback($input, $output);
        $loop = Loop::get();
        $loop->addPeriodicTimer($this->loopInterval, $callback);
        $loop->run();
    }
}
