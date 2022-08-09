<?php

namespace ZnLib\Console\Domain\Libs;

use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use ZnLib\Console\Symfony4\Helpers\InputHelper;

class IO
{

    private $input;
    private $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output  = $output;
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getHelper(): HelperInterface {
        $helperSet = InputHelper::helperSet();
        return $helperSet->get('question');
    }

    public function writeTitle($title)
    {
        $this->output->writeln(['', "<fg=white># $title</>", '']);
    }

    public function askHiddenResponse($message)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new Question($message);
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        return $helper->ask($this->input, $this->output, $question);
    }

    public function ask($message)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new Question($message);
        return $helper->ask($this->input, $this->output, $question);
    }
}
