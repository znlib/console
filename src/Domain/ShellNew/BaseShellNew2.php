<?php

namespace ZnLib\Console\Domain\ShellNew;

use ZnLib\Console\Domain\Base\BaseShellNew;
use ZnLib\Console\Domain\Helpers\CommandLineHelper;
use function Deployer\get;

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

    public function setSudo(bool $sudo): void
    {
        $this->shell->setSudo($sudo);
    }

    public function sudo($sudo = true) {
        $this->sudo = $sudo;
    }*/

    public function runCommand($command, ?string $path = null): string
    {
        $sudoCmdTpl = static::getSudoCommandTemplate();
        $commands = explode('&&', $command);
        foreach ($commands as &$commandItem) {
            $commandItem = trim($commandItem);
            if($this->isSudo($commandItem)) {
                $commandItem = $this->stripSudo($commandItem);
                $commandItem = str_replace('{command}', $commandItem, $sudoCmdTpl);
            }
            //dump($commandItem);
        }
        $command = implode(' && ', $commands);
        //dump();


        return $this->shell->runCommand($command, $path);
    }

    protected static $sudoCommandName = 'sudo';

    protected function runSudo(string $command, $options = [])
    {
        $sudoCmdTpl = static::getSudoCommandTemplate();
        if ($sudoCmdTpl) {
            $command = str_replace('{command}', $command, $sudoCmdTpl);
        }
        return static::_run($command, $options);
    }

    protected static function getSudoCommandTemplate()
    {
        return 'sudo -S {command} < ~/sudo-pass';
    }

    protected static function getSudoCommandName(): string
    {
        return static::$sudoCommandName . ' ';
    }

    protected function stripSudo(string $command): string
    {
        $command = trim($command);
        $command = substr($command, strlen(static::getSudoCommandName()));
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
