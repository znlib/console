<?php

namespace ZnLib\Console\Symfony4\Style;

use ZnLib\Console\Symfony4\Question\ChoiceQuestion;

class SymfonyStyle extends \Symfony\Component\Console\Style\SymfonyStyle
{

    public function choiceMulti(string $question, array $choices, $default = null)
    {
        if (null !== $default) {
            $values = array_flip($choices);
            $default = $values[$default] ?? $default;
        }

        $choiceQuestion = new ChoiceQuestion($question, $choices, $default);
        $choiceQuestion->setMultiselect(true);
        return $this->askQuestion($choiceQuestion);
    }

    public function choice(string $question, array $choices, $default = null)
    {
        if (null !== $default) {
            $values = array_flip($choices);
            $default = $values[$default] ?? $default;
        }

        $choiceQuestion = new ChoiceQuestion($question, $choices, $default);
        return $this->askQuestion($choiceQuestion);
    }
}
