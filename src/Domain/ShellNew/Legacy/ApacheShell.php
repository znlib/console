<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use Deployer\ServerApache;
use ZnCore\Text\Helpers\TemplateHelper;
use ZnLib\Console\Domain\ShellNew\BaseShellNew2;
use ZnLib\Console\Domain\ShellNew\FileSystemShell;
use ZnTool\Deployer\Entities\ApacheStatusEntity;
use function Deployer\get;

class ApacheShell extends BaseShellNew2
{

    public function enableAutorun() {
        $this->runCommand('sudo systemctl enable apache2');
    }

    public function enableRewrite() {
        $this->runCommand('sudo a2enmod rewrite');
    }

    public function restart()
    {
        $this->runCommand('sudo systemctl restart apache2');
    }

    public function start()
    {
        $this->runCommand('sudo systemctl start apache2');
    }
    
    public function status(): ApacheStatusEntity
    {
        $output = $this->runCommand('sudo systemctl status apache2');
        
        $statusEntity = new ApacheStatusEntity();
        
        $isActive = preg_match('/(Active:\s+active\s+\(running\))/i', $output, $matches);
        $statusEntity->setStatus($isActive ? 'active' : '');

        preg_match('/Active:.+;\s*(.+)\s+ago/i', $output, $matches);
        $statusEntity->setAgo($matches[1]);

        preg_match('/Process:\s*(\d+)/i', $output, $matches);
        $statusEntity->setProcessId($matches[1]);

        preg_match('/Main\sPID:\s*(\d+)/i', $output, $matches);
        $statusEntity->setMainPid($matches[1]);

        preg_match('/Tasks:\s*(\d+)/i', $output, $matches);
        $statusEntity->setTaksCount($matches[1]);

        preg_match('/Memory:\s*(\d+)/i', $output, $matches);
        $statusEntity->setMemory($matches[1]);
        
        return $statusEntity;
    }

    public function removeConf(string $domain)
    {
        $file = $domain . '.conf';
        $fs = new FileSystemShell($this->shell);
        $fs->removeFile('/etc/apache2/sites-available/' . $file);
    }

    public function addConf(string $domain, string $directory)
    {
        $this->removeConf($domain);

        $template = '<VirtualHost *:80>
    ServerName {{domain}}
    DocumentRoot {{directory}}
</VirtualHost>';
        $code = TemplateHelper::render($template, [
            'domain' => $domain,
            'directory' => $directory,
        ], '{{', '}}');
        $file = $domain . '.conf';
        //$fs = $this->fsClassName();

        $fs = new FileSystemShell($this->shell);
        $fs->uploadContentIfNotExist($code, '/etc/apache2/sites-available/' . $file);
    }
}
