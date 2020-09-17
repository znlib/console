<?php

namespace ZnLib\Console\Symfony4\Widgets;

class MatrixWidget extends BaseWidget
{

    public function draw(array $matrix)
    {
        foreach ($matrix as $lines) {
            foreach ($lines as $row) {
                $this->output->write("<bg=$row>  </>");
            }
            $this->output->writeln('');
        }
    }

}

/*
 * $matrixWidget = new MatrixWidget($output);
        $matrixWidget->draw([
            ['red', 'green', 'blue', 'black', 'yellow', 'magenta', 'cyan', 'white', 'default'],
            ['red', 'green', 'blue'],
        ]);
 */