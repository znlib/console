<?php

namespace ZnLib\Console\Symfony4\Widgets;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Yaml;

class TextWidget extends BaseWidget
{

    public function writelnList(array $items, bool $newline = true, int $options = 0)
    {
        $this->writeList($items, $newline, $options);
    }

    public function writeList(array $items, bool $newline = false, int $options = 0)
    {
        $message = Yaml::dump($items, 10);
        $this->output->write($message, $newline, $options);
    }

    public function confirm(string $message)
    {
        $question = new ConfirmationQuestion($message . ' (y|n) [n]: ', false);
        $helper = new QuestionHelper;
        $answer = $helper->ask($input, $output, $question);
        return $answer;
    }

}
