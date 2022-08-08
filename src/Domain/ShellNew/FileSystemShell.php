<?php

namespace ZnLib\Console\Domain\ShellNew;

use Deployer\ServerFs;
use Symfony\Component\Process\Process;
use ZnCore\Env\Helpers\TempHelper;
use ZnCore\FileSystem\Helpers\FileHelper;
use ZnCore\FileSystem\Helpers\FileStorageHelper;
use ZnLib\Console\Domain\Base\BaseShellNew;
use ZnLib\Console\Domain\Libs\ShellParsers\ShellItemsParser;
use function Deployer\download;
use function Deployer\upload;

class FileSystemShell extends BaseShellNew2
{

    const A_W = 'a+w';
    const UGO_RWX = 'ugo+rwx';

    public function uploadFile($source, $destination, array $config = [])
    {
        $this->makeDirectory(dirname($destination));
        $dsn = $this->shell->getHostEntity()->getUser() . '@' . $this->shell->getHostEntity()->getHost();
        $port = $this->shell->getHostEntity()->getPort();
        $commandString = "scp -P $port $source $dsn:$destination";
        return $this->shell->runCommandRaw($commandString);
    }

    public function downloadFile($source, $destination, array $config = [])
    {
        FileHelper::createDirectory(dirname($destination));
        $dsn = $this->shell->getHostEntity()->getUser() . '@' . $this->shell->getHostEntity()->getHost();
        $port = $this->shell->getHostEntity()->getPort();
        $commandString = "scp -P $port $dsn:$source $destination";
        return $this->shell->runCommandRaw($commandString);
    }




    public function uploadContent(string $content, string $destination)
    {
        $dir = TempHelper::getTmpDirectory('deployer_upload');
        $file = basename($destination);
        $fileName = $dir . '/' . $file;
        FileStorageHelper::save($fileName, $content);
        $this->uploadFile($fileName, $destination);
    }

    public function downloadContent(string $source): string
    {
        $dir = TempHelper::getTmpDirectory('deployer_upload');
        $file = basename($source);
        $fileName = $dir . '/' . $file;
        $this->downloadFile($source, $fileName);
        return FileStorageHelper::load($fileName);
    }




    public function modifyFileWithCallback(string $file, callable $callback): void
    {
        $content = $this->downloadContent($file);
        $content = call_user_func_array($callback, [$content]);
        $this->uploadContent($content, $file);
    }

    public function uploadIfNotExist(string $source, string $dest): bool
    {
        if ($this->isFileExists($dest)) {
            return false;
        }
        $this->uploadFile($source, $dest);
        return true;
    }

    public function uploadContentIfNotExist(string $content, string $dest): bool
    {
        if ($this->isFileExists($dest)) {
            return false;
        }
        $this->uploadContent($content, $dest);
        return true;
    }




    public function makeLink(string $filePath, string $linkPath, string $options = '-nfs'): bool
    {
        return $this->runCommand("ln $options $filePath $linkPath");
    }

    public function move(string $from, string $to, string $options = ''): bool
    {
        return $this->runCommand("mv $options $from $to");
    }

    public function chmodRecurse(string $path, string $options)
    {
        $this->chmod($path, $options, true);
//        $this->runCommand("chmod -R $options $path");
    }

    public function chmod(string $path, string $options, bool $isRecursive = false)
    {
        $recursive = $isRecursive ? '-R' : '';
        $this->runCommand("chmod $recursive $options $path");
    }

    public function chown(string $path, string $owner)
    {
        $this->runCommand("chown $owner $path");
    }




    public function list(string $path): array
    {
        $commandOutput = $this->runCommand("cd $path && ls -la", $path);
        $parser = new ShellItemsParser([$this, 'parseLine'], [$this, 'filterItem']);
        $items = $parser->parse($commandOutput);
        return $items;
    }

    public function removeFile(string $path)
    {
        if (!$this->isFileExists($path)) {
            return false;
        }
        $this->runCommand("rm $path");
    }

    public function removeDir(string $path)
    {
        if (!$this->isDirectoryExists($path)) {
            return false;
        }
        $this->runCommand("rm -rf $path");
    }

    public function makeDirectory(string $directory)
    {
        $this->runCommand("mkdir -p $directory");
    }

    public function touch(string $file)//: bool
    {
        $this->runCommand("touch $file");
    }

    public function isDirectoryExists(string $file): bool
    {
        $out = $this->runCommand("test -d $file && echo true || echo false");
        return trim($out) == 'true';

//        return $this->test("[ -d $file ]");
    }

    public function isFileExists(string $file): bool
    {
        $out = $this->runCommand("test -e $file && echo true || echo false");
        return trim($out) == 'true';
//        dd();
        // "[ -f $file ]"
        return $this->test("test -e $file && echo true || echo false");
    }

    public function filterItem(array $item): bool
    {
        return !in_array($item['fileName'], ['.', '..']);
    }

    public function parseLine(string $line): ?array
    {
        $isMatch = preg_match('/(\S+)\s+(\d+)\s+(\S+)\s+(\S+)\s+(\d+)\s+(\S+)\s+(\d+)\s+(\S+)\s+(\S+)/ix', $line, $matches);
        if ($isMatch) {
            $item = [
                'permission' => $matches[1],
                'type' => intval($matches[2]),
                'user' => $matches[3],
                'group' => $matches[4],
                'size' => intval($matches[5]),
                'month' => $matches[6],
                'day' => intval($matches[7]),
                'time' => $matches[8],
                'fileName' => $matches[9],
            ];
            $types = [
                1 => 'file',
                2 => 'dir',
                3 => 'sys',
            ];
            $item['type'] = $types[$item['type']] ?? $item['type'];
            return $item;
        }
        return null;
    }
}
