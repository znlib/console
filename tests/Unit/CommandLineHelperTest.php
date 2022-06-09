<?php

namespace ZnLib\Console\Tests\Unit;

use ZnLib\Console\Symfony4\Helpers\CommandLineHelper;
use ZnTool\Test\Base\BaseTest;

final class CommandLineHelperTest extends BaseTest {

    public function testArgsToStringWithLang()
    {
        $channel = null;
        $commandString = CommandLineHelper::argsToString([
            'php',
            'zn',
            'queue:run',
            $channel,
            "--wrapped" => 1,
        ], 'en_GB');

        $this->assertSame("LANG=en_GB php 'zn' 'queue:run' --wrapped=1", $commandString);

        $commandString = CommandLineHelper::argsToString("php zn queue:run --wrapped=1", 'en_GB');
        $this->assertSame("LANG=en_GB php zn queue:run --wrapped=1", $commandString);
    }

    public function testArgsToString() {
        $commandString = CommandLineHelper::argsToString("php zn queue:run --wrapped=1");
        $this->assertSame("php zn queue:run --wrapped=1", $commandString);


        $channel = null;
        $commandString = CommandLineHelper::argsToString([
            'php',
            'zn',
            'queue:run',
            $channel,
            "--wrapped" => 1,
        ]);

        $this->assertSame("php 'zn' 'queue:run' --wrapped=1", $commandString);

        $channel = 'email';
        $commandString = CommandLineHelper::argsToString([
            'php',
            'zn',
            'queue:run',
            $channel,
            "--wrapped" => 1,
        ]);

        $this->assertSame("php 'zn' 'queue:run' 'email' --wrapped=1", $commandString);

        $channel = 'email';
        $commandString = CommandLineHelper::argsToString([
            'php',
            'zn',
            'queue:run',
            $channel,
            "--wrapped" => false,
        ]);

        $this->assertSame("php 'zn' 'queue:run' 'email' --wrapped=0", $commandString);

        $channel = 'email';
        $commandString = CommandLineHelper::argsToString([
            'php',
            'zn',
            'queue:run',
            $channel,
            "--wrapped",
        ]);

        $this->assertSame("php 'zn' 'queue:run' 'email' '--wrapped'", $commandString);

        $channel = 'email';
        $commandString = CommandLineHelper::argsToString([
            'php',
            'zn',
            'queue:run',
            'channel' => $channel,
            "--wrapped",
        ]);

        $this->assertSame("php 'zn' 'queue:run' channel='email' '--wrapped'", $commandString);
    }

}