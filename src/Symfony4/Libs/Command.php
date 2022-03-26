<?php

namespace ZnLib\Console\Symfony4\Libs;

class Command
{

    private $commands = [];

    public function add(string $cmd): self {
        $this->commands[] = $cmd;
        return $this;
    }

    public function toString() {
        return implode(' && ', $this->commands);
    }
}
