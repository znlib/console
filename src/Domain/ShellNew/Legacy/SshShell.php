<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use Deployer\ServerFs;
use Deployer\SshConfig;
use ZnLib\Console\Domain\ShellNew\BaseShellNew2;

class SshShell extends BaseShellNew2
{

    public function list()
    {
        $output = $this->run('ssh-add -l');
        return explode(PHP_EOL, $output);
    }

    public function getList(): array
    {
        $sshConfig = self::getSshConfig();
        return $sshConfig->getConfig();
    }

    protected function getSshConfig(): SshConfig
    {
        $sshConfig = new SshConfig();
        $configData = [];
        if (ServerFs::isFileExists('~/.ssh/config')) {
            $config = ServerFs::downloadContent('~/.ssh/config');
            $configData = $sshConfig->parse($config);
        }
        return $sshConfig;
    }

    public function setConfig(array $keyList)
    {
        $sshConfig = self::getSshConfig();
//        $configData = $sshConfig->getConfig();
//    $indexed = ArrayHelper::index($configData, 'name');

        foreach ($keyList as $item) {
            $keyName = $item['name'];
            $domain = $item['host'];
//        $isExists = isset($indexed[$keyName]);
            if (!$sshConfig->hasByName($keyName)) {
                $sshConfig->add($keyName, $domain, "~/.ssh/$keyName");
                /*$configData[] = [
                    'name' => $keyName,
                    'host' => $domain,
                    'path' => "~/.ssh/$keyName",
                ];*/
            }
        }

        $code = $sshConfig->generate();

        ServerFs::uploadContent($code, '~/.ssh/config');
    }

    public function add(string $key)
    {
        $this->runCommand("ssh-add $key");
    }

    public function generate(string $keyFile, string $type = 'rsa', int $bit = 2048, string $email = 'my@example.com')
    {
        $this->runCommand("ssh-keygen -t $type -b 2048 -C \"$email\" -f $keyFile -N \"\"");
    }

    public function runAgent()
    {
        $this->runCommand('eval $(ssh-agent)');

        /*    $this->runCommand('if ps -p $SSH_AGENT_PID > /dev/null
    then
       echo "ssh-agent is already running"
    else
        eval $(ssh-agent)
    fi');*/

    }

    public function uploadKey(string $source)
    {
        $dest = "~/.ssh/$source";
        $isUploadedPrivateKey = $this->fsClass()::uploadFile("{{ssh_directory}}/$source", $dest);
        $isUploadedPublicKey = $this->fsClass()::uploadFile("{{ssh_directory}}/$source.pub", "$dest.pub");
        if ($isUploadedPrivateKey || $isUploadedPublicKey) {
            $this->runAgent();
            $this->runCommand("ssh-add -D $dest");
            $this->runCommand("ssh-add $dest");
        }
    }
}
