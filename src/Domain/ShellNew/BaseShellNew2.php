<?php

namespace ZnLib\Console\Domain\ShellNew;

use ZnLib\Console\Domain\Base\BaseShellNew;

class BaseShellNew2
{

    protected $shell;
    private $sudo = false;

    public function isSudo(): bool
    {
        return $this->sudo;
    }

    public function setSudo(bool $sudo): void
    {
        $this->sudo = $sudo;
    }

    public function sudo($sudo = true) {
        $this->sudo = $sudo;
    }

    public function runCommand($command, ?string $path = null): string
    {
        return $this->shell->runCommand($command, $path);
    }

    public function test($command, ?string $path = null): string
    {
        return $this->shell->test($command, $path);
    }

    public function __construct(BaseShellNew $shell)
    {
        $this->shell = $shell;
    }
}
