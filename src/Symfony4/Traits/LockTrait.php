<?php

namespace ZnLib\Console\Symfony4\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Exception\LockAcquiringException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

trait LockTrait
{

    private $defaultLockerName;

    /** @var LockInterface[] */
    private $lockers = [];

    /** @var LockFactory */
    private $lockFactory;

//    abstract public function getInput(): InputInterface;

//    abstract public function getOutput(): OutputInterface;

    abstract protected function runProcess(): void;

    protected function setDefaultLockerName(?string $defaultLockerName): void
    {
        $this->defaultLockerName = $defaultLockerName;
    }

    protected function setLockFactory(LockFactory $lockFactory): void
    {
        $this->lockFactory = $lockFactory;
    }

    protected function refreshLock(string $name = null): void
    {
        $name = $this->forgeLockerName($name);
        $lock = $this->getLokerByName($name);
        $lock->refresh();
    }

    protected function lock(string $name = null): void
    {
        $name = $this->forgeLockerName($name);
        $lock = $this->getLokerByName($name);
        if ($lock->acquire()) {
            /*try {

            } finally {
                $this->lock->release();
            }*/
        } else {
            throw new LockAcquiringException('Already started!');
        }
    }

    protected function unlock(string $name = null): void
    {
        $name = $this->forgeLockerName($name);
        $lock = $this->getLokerByName($name);
        $lock->release();
    }

    protected function runProcessWithLock(string $name): void
    {
//        $output = $this->getOutput();
        $this->setDefaultLockerName($name);
//        $name = $this->forgeLockerName($name);
        try {
            $this->lock($name);
            $this->runProcess();
        /*} catch (LockAcquiringException $e) {
            $output->writeln('<fg=yellow>' . $e->getMessage() . '</>');
            $output->writeln('');*/
        } finally {
            $this->unlock($name);
        }
    }

    private function forgeLockerName(?string $name = null): string
    {
        return $name ?? $this->defaultLockerName;
    }

    private function getLokerByName(string $name): LockInterface
    {
        if (!isset($this->lockers[$name])) {
            $this->lockers[$name] = $this->lockFactory->createLock($name, 30);
        }
        return $this->lockers[$name];
    }
}
