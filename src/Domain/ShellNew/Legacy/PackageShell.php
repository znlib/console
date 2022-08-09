<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use Deployer\View;
use ZnLib\Console\Domain\ShellNew\BaseShellNew2;

class PackageShell extends BaseShellNew2
{

    public function addRepository($package, $options = [])
    {
        return $this->runCommand("sudo add-apt-repository -y $package");
    }

    public function install($package, $options = [])
    {
        if ($this->isInstalled($package)) {
            echo "$package alredy exist\n";
            return false;
        } else {
//            $this->shell->sudo();
            $result = $this->runCommand("sudo apt-get install $package -y");
            echo "$package installed\n";
            return $result ?: true;
        }
    }

    public function installBatch(array $packages)
    {
        $exists = $new = [];
        foreach ($packages as $package) {
            if ($this->isInstalled($package)) {
                $exists[] = $package;
            } else {
                $new[] = $package;
            }
        }
        foreach ($exists as $package) {
            echo "$package alredy exist\n";
        }
        $packagesString = implode(' ', $new);
        if (trim($packagesString) != '') {
            $this->install($packagesString);
            foreach ($new as $package) {
                echo "$package installed\n";
            }
        }
    }

    public function update()
    {
        return $this->runCommand('sudo apt-get update -y');
    }

    public function isInstalled(string $package): bool
    {
        $list = $this->find($package);
        return !empty($list);
    }

    protected function find(string $package)
    {

        try {
            $result = $this->runCommand("dpkg-query --list | grep -i $package");
        } catch (\Throwable $e) {
            return [];
        }
        $result = trim($result);
        $list = explode(PHP_EOL, $result);
        return $list;
    }

}
