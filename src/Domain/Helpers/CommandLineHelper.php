<?php

namespace ZnLib\Console\Domain\Helpers;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CommandLineHelper
{

    private static $optionGlue = '=';

    /**
     * @param Process | string | array $command
     */
    public static function run($command)
    {
        $process = self::createProcessInstance($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    private static function createProcessInstance($command): Process
    {
        if ($command instanceof Process) {
            $process = $command;
        } elseif (is_string($command) || is_array($command)) {
            $process = Process::fromShellCommandline($command);
        } else {
            throw new \Exception('Bad command format!');
        }
        return $process;
    }

    public static function argsToString($command, string $langCode = null): string
    {
        $langOption = self::generateLang($langCode);
        if (is_array($command)) {
            $commandName = array_shift($command);
            $arguments = self::generateCommand($command);
            $command = "{$commandName} {$arguments}";
        }
        return trim("{$langOption} $command");
    }

    protected static function generateCommand(array $args): string
    {
        $args = self::processCommandArgs($args);
        return implode(' ', $args);
    }

    protected static function generateLang(?string $lang = null): string
    {
        if ($lang) {
            return 'LANG=' . $lang;
        }
        return '';
    }

    protected static function escapeshellarg($arg): string
    {
        if (is_bool($arg)) {
            $arg = intval($arg);
        }
        if (is_int($arg)) {
            return strval($arg);
        }
        return escapeshellarg($arg);
    }

    protected static function cleanEmptyArgs(array $args)
    {
        foreach ($args as $key => $value) {
            if (empty($value)) {
                if (is_string($key) && !empty($key)) {
                    $args[$key] = false;
                } else {
                    unset($args[$key]);
                }
            }
        }
        return $args;
    }

    protected static function processCommandArg($key, $value)
    {
        $cmd = '';
        if (is_string($key)) {
            $cmd = $key . self::$optionGlue;
        }
        return $cmd . self::escapeshellarg($value);
    }

    protected static function processCommandArgs(array $args)
    {
        $args = self::cleanEmptyArgs($args);
        $cmdArr = [];
        foreach ($args as $key => $value) {
            $cmdArr[] = self::processCommandArg($key, $value);
        }
        return $cmdArr;
    }
}
