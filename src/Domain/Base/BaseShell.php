<?php

namespace ZnLib\Console\Domain\Base;

use ZnCore\Env\Enums\OsFamilyEnum;
use ZnCore\Env\Helpers\OsHelper;
use ZnLib\Console\Domain\Exceptions\ShellException;

abstract class BaseShell
{

    /** @var  string */
    private $path;
    /** @var  string|NULL  @internal */
    private $cwd;

    private $lang = 'en_GB';

    /**
     * @param $path string
     *
     * @throws ShellException
     */
    public function __construct($path)
    {
        $this->setPath($path);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param $path string
     *
     * @throws ShellException
     */
    public function setPath($path)
    {
        $this->path = realpath($path);
        if ($this->path === false) {
            throw new ShellException("Path '$path' not found.");
        }
    }

    /**
     * @return static
     */
    protected function begin()
    {
        if ($this->cwd === null) {
            $this->cwd = getcwd();
            chdir($this->path);
        }
        return $this;
    }


    /**
     * @return static
     */
    protected function end()
    {
        if (is_string($this->cwd)) {
            chdir($this->cwd);
        }
        $this->cwd = null;
        return $this;
    }

    /**
     * @param $cmd string
     * @param $filter callback|NULL
     *
     * @return string[]|NULL
     * @throws ShellException
     */
    protected function extractFromCommand($cmd, $filter = null)
    {
        $output = [];
        $exitCode = null;
        $this->begin();
        $cmd = $this->prepareCmd($cmd);
        exec("$cmd", $output, $exitCode);
        $this->end();
        if (/*$exitCode !== 0 || */ !is_array($output)) {
            throw new ShellException("Command $cmd failed.");
        }
        /** @var string $filter */
        if ($filter !== null) {
            $newArray = [];
            foreach ($output as $line) {
                $value = $filter($line);
                if ($value === false) {
                    continue;
                }
                $newArray[] = $value;
            }
            $output = $newArray;
        }
        if (!isset($output[0])) // empty array
        {
            return null;
        }
        return $output;
    }

    /**
     * Runs command.
     *
     * @param $cmd string|array
     *
     * @return static
     * @throws ShellException
     */
    protected function prepareCmd(string $cmd): string
    {
        if (OsHelper::isFamily(OsFamilyEnum::LINUX)) {
            $cmd = 'LANG=' . $this->lang . ' ' . $cmd;
        }
        return $cmd;
    }

    protected function runExec(string $cmd, &$output = null)
    {
        $cmd = $this->prepareCmd($cmd);
        return exec($cmd, $output);
    }

    protected function run($cmd/*, $options = NULL*/)
    {
        $args = func_get_args();
        $cmd = $this->processCommand($args);
        $cmd = $this->prepareCmd($cmd);
            exec($cmd . ' 2>&1', $output, $ret);
        if ($ret !== 0) {
            throw new ShellException("Command '$cmd' failed (exit-code $ret).", $ret);
        }
        return $this;
    }

    protected static function processCommand(array $args)
    {
        $cmd = [];
        $programName = array_shift($args);
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $key => $value) {
                    $_c = '';
                    if (is_string($key)) {
                        $_c = "$key ";
                    }
                    $cmd[] = $_c . escapeshellarg($value);
                }
            } elseif (is_scalar($arg) && !is_bool($arg)) {
                $cmd[] = escapeshellarg($arg);
            }
        }
        return "$programName " . implode(' ', $cmd);
    }

}
