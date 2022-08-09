<?php

namespace ZnLib\Console\Domain\ShellNew;

use ZnLib\Console\Domain\Base\BaseShellNew;

class BaseShellNew2
{

    protected $shell;
    private $sudo = false;

    public function __construct(BaseShellNew $shell)
    {
        $this->shell = $shell;
    }

    /*public function isSudo(): bool
    {
        return $this->shell->isSudo();
    }

    public function sudo($sudo = true) {
        $this->sudo = $sudo;
    }*/

    public function setSudo(bool $sudo): void
    {
        $this->shell->setSudo($sudo);
    }

    public function sudo($sudo = true): self
    {
        $this->sudo = $sudo;
        $clone = clone $this;
        $clone->setSudo($sudo);
        return $clone;
    }

    protected function prepareSudo(string $command): string
    {
        $sudoCmdTpl = static::getSudoCommandTemplate();
        $commands = explode('&&', $command);
        foreach ($commands as &$commandItem) {
            $commandItem = trim($commandItem);
            if ($this->isSudo($commandItem) || $this->sudo) {
                $commandItem = $this->stripSudo($commandItem);
                $commandItem = str_replace('{command}', $commandItem, $sudoCmdTpl);
            }
        }
        $command = implode(' && ', $commands);
        return $command;
    }

    public function runCommand($command, ?string $path = null): string
    {
        $command = $this->prepareSudo($command);
        return $this->shell->runCommand($command, $path);
    }

    protected static function getSudoCommandTemplate()
    {
        $config = include($_ENV['DEPLOYER_CONFIG_FILE']);
        return $config['connections']['default']['sudo']['commandTemplate'] ?? 'sudo {command}';
//        return 'sudo -S {command} < ~/sudo-pass';
    }

    protected static function getSudoCommandName(): string
    {
        $config = include($_ENV['DEPLOYER_CONFIG_FILE']);
        return ($config['connections']['default']['sudo']['command'] ?? 'sudo') . ' ';
    }

    protected function stripSudo(string $command): string
    {
        $command = trim($command);
        $command = preg_replace('/^('.static::getSudoCommandName().'\s+)/i', '', $command);
//        dd($command);
//        $command = substr($command, strlen(static::getSudoCommandName()));
        return $command;
    }

    protected function isSudo(string $command): bool
    {
        $command = trim($command);
        return strpos($command, static::getSudoCommandName()) === 0;
    }


    /*public function test($command, ?string $path = null): string
    {
        return $this->shell->test($command, $path);
    }*/
}
