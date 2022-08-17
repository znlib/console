<?php

namespace ZnLib\Console\Domain\Libs;

use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use ZnCore\Arr\Helpers\ArrayHelper;
use ZnLib\Console\Symfony4\Helpers\InputHelper;
use ZnLib\Console\Symfony4\Question\ChoiceQuestion;

class IO
{

    private $input;
    private $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getHelper(): HelperInterface
    {
        $helperSet = InputHelper::helperSet();
        return $helperSet->get('question');
    }

    public function writeTitle($title)
    {
        $this->output->writeln(['', "<fg=white># $title</>", '']);
    }

    public function writeSubTitle($title)
    {
        $this->output->writeln(['', "<fg=white>- $title</>", '']);
    }

    public function warning($title)
    {
        $this->output->writeln("<fg=yellow>$title</>");
    }

    public function success($title)
    {
        $this->output->writeln(['', "<fg=green>$title</>", '']);
    }

    public function writeln(...$args)
    {
        $this->output->writeln(...$args);
    }

    public function write(...$args)
    {
        $this->output->write(...$args);
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
        $question = new Question($message);
        return $this->helperAsk($question);

//        /** @var QuestionHelper $helper */
//        $helper = $this->getHelper('question');
//        return $helper->ask($this->input, $this->output, $question);
    }

    protected function helperAsk(Question $question) {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        return $helper->ask($this->input, $this->output, $question);
    }

    public function choiceQuestion($message, $choices)
    {
        $this->writeln('');
        $question = new ChoiceQuestion($message, array_values($choices));
        $selected = $this->helperAsk($question);
        return $this->prepareSelected([$selected], $choices);
    }

    public function multiChoiceQuestion($message, $choices)
    {
        $this->writeln('');
        $question = new ChoiceQuestion($message, array_values($choices));
        $question->setMultiselect(true);
        $selected = $this->helperAsk($question);
        return $this->prepareSelected($selected, $choices);
    }

    protected function prepareSelected($selected, $choices)
    {
        if(ArrayHelper::isIndexed($choices)) {
            return $selected;
        }
        
        $keyList = array_keys($choices);
        $titleList = array_values($choices);
        $new = [];
        foreach ($titleList as $index => $title) {
            if(in_array($title, $selected)) {
                $new[] = $keyList[$index];
            }
        }
        return $new;
    }
}
