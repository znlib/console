<?php

namespace ZnLib\Console\Symfony4\Helpers;

use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class InputHelper
{

    public static function question(InputInterface $input, OutputInterface $output, string $questionText, string $default = ''): string
    {
        $question = new Question($questionText, $default);
        $helperSet = self::helperSet();
        /** @var QuestionHelper $helper */
        $helper = $helperSet->get('question');
        $answer = $helper->ask($input, $output, $question);
        return $answer;
    }

    public static function helperSet()
    {
        return new HelperSet([
            new FormatterHelper(),
            new DebugFormatterHelper(),
            new ProcessHelper(),
            new QuestionHelper(),
        ]);
    }
}
