<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use Deployer\Git;
use ZnLib\Console\Domain\ShellNew\BaseShellNew2;

class GitShell extends BaseShellNew2
{

    private $directory;

    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    public function createTag(string $name)
    {
        $command = "tag '$name'";
        return $this->runGit($command);
    }

    public function clone(string $repository, string $branch = null, string $directory = '.')
    {
        $command = "clone ";
        if ($branch) {
            $command .= " -b $branch ";
        }
        $command .= " -q --depth 1 $repository $directory ";
        return $this->runGit($command);
    }

    public function add(string $paths = '.')
    {
        $command = "add $paths";
        return $this->runGit($command);
    }

    public function commit(string $message = 'upd')
    {
        $command = "commit -m $message";
        return $this->runGit($command);
    }

    public function pull()
    {
        $command = "pull";
        return $this->runGit($command);
    }

    public function push(string $branchName = null, string $target = 'origin')
    {
        $command = "push";
        if ($branchName) {
            $command .= " $target '$branchName' ";
        }
        return $this->runGit($command);
    }

    public function stash()
    {
        $command = "stash";
        return $this->runGit($command);
    }

    public function isHasChanges(): bool
    {
        $output = $this->status();
        return strpos($output, 'nothing to commit') !== false;
    }

    public function status()
    {
        $command = "status";
        return $this->runGit($command);
    }

    public function checkout(string $branch)
    {
        $command = "checkout $branch";
        return $this->runGit($command);
    }

    public function config(string $key, string $value, bool $isGlobal = false)
    {
        $command = "config ";
        if ($isGlobal) {
            $command .= " --global ";
        }
        $command .= " $key \"$value\" ";
        return $this->runGit($command);
    }

    public function configList()
    {
        $configCode = $this->runGit('config --list');
        $configLines = explode(PHP_EOL, $configCode);
        $config = [];
        foreach ($configLines as $line) {
            if (!empty($line)) {
                list($name, $value) = explode('=', $line);
                if ($name) {
                    $config[$name] = $value;
                }
            }
        }
        return $config;
    }

    protected function runGit($command)
    {
        return $this->shell->runCommand("cd {$this->directory} && {{bin/git}} $command");
//        return $this->consoleClass()::run("{{bin/git}} $command");
    }
}
