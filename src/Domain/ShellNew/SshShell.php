<?php

namespace ZnLib\Console\Domain\ShellNew;

class SshShell extends BaseShellNew2
{

    public function runAgent()
    {
        $this->shell->runCommand('eval $(ssh-agent)');

        /*    static::run('if ps -p $SSH_AGENT_PID > /dev/null
    then
       echo "ssh-agent is already running"
    else
        eval $(ssh-agent)
    fi');*/

    }

    public function add(string $destFilename)
    {
        $this->shell
            ->addCommand("eval $(ssh-agent)")
            ->addCommand("ssh-add -D $destFilename")
            ->addCommand("ssh-add $destFilename")
            ->run();

//        $this->runAgent();
//        $this->runCommand("ssh-add -D $destFilename");
//        $this->runCommand("ssh-add $destFilename");
    }
}
