<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NasaRoversCommand extends Command
{
    protected static $defaultName = 'app:nasa-rovers';
    protected static $defaultDescription = 'Command for navigate NASA rovers on the Mars';

    protected function configure(): void
    {
        $this->setHelp(
            "A squad of robotic rovers is to be landed by NASA on a plateau on Mars.
            This plateau, which is curiously rectangular, must be navigated by the rovers so that their on
            board cameras can get a complete view of the surrounding terrain to send back to Earth.
            A rover's position is represented by a combination of an x and y co-ordinates and a letter
            representing one of the four cardinal compass points. The plateau is divided up into a grid to
            simplify navigation. An example position might be 0, 0, N, which means the rover is in the
            bottom left corner and facing North.
            In order to control a rover, NASA sends a simple string of letters. The possible letters are 'L', 'R'
            and 'M'. 'L' and 'R' makes the rover spin 90 degrees left or right respectively, without moving
            from its current spot.
            'M' means move forward one grid point, and maintain the same heading.
            Assume that the square directly North from (x, y) is (x, y+1)."
        );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

    }
}