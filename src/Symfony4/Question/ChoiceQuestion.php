<?php

namespace ZnLib\Console\Symfony4\Question;

class ChoiceQuestion extends \Symfony\Component\Console\Question\ChoiceQuestion
{

    public function __construct(string $question, array $choices, $default = null)
    {

        parent::__construct($question, $choices, $default);
        $this->setNormalizer(function ($value) {
            if ($value == 'a') {
                $choices = $this->getChoices();
                unset($choices['a']);
                $value = implode(',', array_keys($choices));
            }
            if (mb_strpos($value, '-') !== false) {
                preg_match('#(\d+)\-(\d+)#iu', $value, $matches);
                $start = $matches[1];
                $end = $matches[2];
                $arr = [];
                for ($i = $start; $i <= $end; $i++) {
                    $arr[] = $i;
                }
                $value = implode(',', $arr);
            }
            return $value;
        });
    }

    public function getChoices()
    {
        $choices = parent::getChoices();
        if ($this->isMultiselect()) {
            $choices['a'] = '[All]';
        }
        return $choices;
    }
}