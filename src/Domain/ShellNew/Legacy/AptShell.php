<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use Deployer\ServerApt;
use Deployer\ServerPackage;
use Deployer\View;
use ZnLib\Console\Domain\ShellNew\BaseShellNew2;

class AptShell extends BaseShellNew2
{

    /*public function addRepository($package, $options = [])
    {
        return $this->runCommand("sudo add-apt-repository -y $package", $options);
    }

    public function install($package, $options = [])
    {
        if (ServerPackage::isInstalled($package)) {
            View::warning("$package alredy exist");
            return false;
        } else {
            $result = $this->runCommand("sudo apt-get install $package -y", $options);
            View::success("$package installed");
            return $result;
        }
    }

    public function installBatch(array $packages)
    {
        $exists = $new = [];
        foreach ($packages as $package) {
            if (ServerApt::isInstalled($package)) {
                $exists[] = $package;
            } else {
                $new[] = $package;
            }
        }
        foreach ($exists as $package) {
            View::warning("$package alredy exist");
        }
        $packagesString = implode(' ', $new);
        if(trim($packagesString) != '') {
            $this->install($packagesString);
            foreach ($new as $package) {
                View::success("$package installed");
            }
        }
    }

    public function update()
    {
        return $this->runCommand('sudo apt-get update -y');
    }

    public function find(string $package)
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

    public function isInstalled(string $package): bool
    {
        $list = $this->find($package);
        return !empty($list);
    }*/

}
