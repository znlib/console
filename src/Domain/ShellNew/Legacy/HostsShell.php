<?php

namespace ZnLib\Console\Domain\ShellNew\Legacy;

use ZnLib\Console\Domain\ShellNew\BaseShellNew2;

class HostsShell extends BaseShellNew2
{

    public function add(string $domain, string $ip = '127.0.0.1')
    {
        $this->remove($domain);
        $content = $this->loadConfig();
        if(!isset($content['lamp'][$domain])) {
            $content['lamp'][$domain] = $ip;
        }
        /*dd($content);
        if (strpos($content, $domain) === false) {
            $content .= PHP_EOL . $ip . ' ' . $domain;
        }*/
        $this->saveConfig($content);
    }

    public function remove(string $domain, string $ip = '127.0.0.1')
    {
        $content = $this->loadConfig();
        foreach ($content as $groupName => $groupLines) {
            if(isset($content[$groupName][$domain])) {
                unset($content[$groupName][$domain]);
            }
        }

        /*if (strpos($content, $domain) !== false) {
            $content = preg_replace("#" . preg_quote($ip) . "\s+" . preg_quote($domain) . "#i", '', $content);
        }*/
        $this->saveConfig($content);
    }

    public function loadConfig() {
        $content = $this->fsClass()::downloadContent('/etc/hosts');

//        dd($content);

        preg_match_all('/\s*\#\s*<(.+)>([^<]+)<\/.+>/i', $content, $matches);
//        dd($matches);
        $cc = [];
        foreach ($matches[1] as $index => $groupName) {
            $hostLines = $matches[2][$index];

            $cc[$groupName] = $this->toLineArr($hostLines);
        }

        if(empty($cc)) {
            $cc['system'] = $this->toLineArr($content);
        }

        return $cc;
    }

    protected function toLineArr($hostLines) {
        $hostArr = explode(PHP_EOL, $hostLines);
        $gg = [];
        foreach ($hostArr as $i => $line) {
            $line = trim($line);
            if($line == '' || $line[0] == '#') {
                unset($hostArr[$i]);
            } else {
                $rr = preg_split("#\s+#i", $line);
                if(count($rr) >= 2) {
                    $gg[$rr[1]] = $rr[0];
                }
            }
        }
        return $gg;
    }

    protected function saveConfig($content) {
        $code = '';
        foreach ($content as $groupName => $linesArr) {
            $groupCode = '';
            foreach ($linesArr as $domain => $ip) {
                $groupCode .= "$ip\t$domain\n";
            }
            $groupCode = trim($groupCode);
            $code .= "\n\n# <$groupName>\n\n$groupCode\n\n# </$groupName>\n\n";
        }
        $this->fsClass()::uploadContent($code, '~/tmp/hosts');
        $this->runCommand('sudo mv -f ~/tmp/hosts /etc/hosts');
    }
}
