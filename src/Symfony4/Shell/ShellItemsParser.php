<?php

namespace ZnLib\Console\Symfony4\Shell;

class ShellItemsParser
{

    private $itemParseCallback;
    private $itemFilterCallback;

    public function __construct(callable $itemParseCallback, callable $itemFilterCallback = null)
    {
        $this->itemParseCallback = $itemParseCallback;
        $this->itemFilterCallback = $itemFilterCallback;
    }

    protected function filter(array $items): array
    {
        $items = array_filter($items, $this->itemFilterCallback);
        return $items;
    }

    public function parse(string $commandOutput): array
    {
        $items = [];
        $lines = explode(PHP_EOL, $commandOutput);
        foreach ($lines as $line) {
            $line = trim($line);
            $item = call_user_func($this->itemParseCallback, $line);
            if ($item) {
                $items[] = $item;
            }
        }
        if ($this->itemFilterCallback) {
            $items = $this->filter($items);
        }
        $items = array_values($items);
        return $items;
    }
}
