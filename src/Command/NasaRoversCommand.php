<?php
declare(strict_types=1);

namespace App\Command;

use App\Application\Enum\Direction;
use App\Application\Repository\RoverRepositoryInterface;
use App\Domain\Entity\Rover;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class NasaRoversCommand extends Command
{
    protected static $defaultName = 'app:nasa-rovers';
    protected static $defaultDescription = 'Command for navigate NASA rovers on the Mars';

    public function __construct(
        private RoverRepositoryInterface $roverRepository,
    ) {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->setHelp(
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
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $question = new Question('Enter top-right coordinates (x y): ');
        $question->setValidator(function ($answer) {
            if (!is_string($answer) || empty($answer) || 1 !== preg_match('/^[0-9]{1,} [0-9]{1,}$/', $answer)) {
                throw new \RuntimeException(
                    'Coordinates should be like X Y. For example, 0 0'
                );
            }

            return $answer;
        });

        $topRightCoordinates = $helper->ask($input, $output, $question);
        [$topRightXCoordinate, $topRightYCoordinate] = explode(' ', $topRightCoordinates);

        $question2 = new Question('Enter start coordinates of the rover (X Y Direction). For example, 1 1 N: ');
        $question2->setValidator(function ($answer) {
            if (!is_string($answer) || empty($answer) || 1 !== preg_match('/^[0-9]{1,} [0-9]{1,} [NSWE]$/', $answer)) {
                throw new \RuntimeException(
                    'Coordinates should be like X Y Direction. For example, 1 1 N'
                );
            }

            return $answer;
        });

        $roverCoordinates = $helper->ask($input, $output, $question2);
        [$roverXPosition, $roverYPosition, $roverDirection] = explode(' ', $roverCoordinates);

        $roverDirectionEnum = match (strtoupper($roverDirection)) {
            'N' => Direction::North,
            'S' => Direction::South,
            'W' => Direction::West,
            'E' => Direction::East,
            default => throw new \Exception('Invalid direction. It can be one of the: N, S, W, E')
        };

        $rover = new Rover((int)$roverXPosition, (int)$roverYPosition, $roverDirectionEnum);

        $question3 = new Question('Enter action commands. For example, MRLMM: ');
        $question3->setValidator(function ($answer) {
            if (!is_string($answer) || empty($answer) || 1 !== preg_match('/^[MLR]{1,}$/', $answer)) {
                throw new \RuntimeException(
                    'Some of the action is invalid. Please, use only M, L or R'
                );
            }

            return $answer;
        });

        /** @var string $actions */
        $actions = $helper->ask($input, $output, $question3);
        foreach(str_split($actions) as $action) {
            match($action) {
                'M' => $this->roverRepository->move($rover),
                'L' => $this->roverRepository->turnLeft($rover),
                'R' => $this->roverRepository->turnRight($rover),
                default => throw new \Exception(
                    'Invalid action. Please, use only M, L or R'
                )
            };
        }

        $output->writeln([
            \sprintf(
                '%d %d %s',
                $rover->getCoordinateX(),
                $rover->getCoordinateY(),
                $rover->getDirection()->toString()
            ),
            "\n"
        ]);

        return Command::SUCCESS;
    }
}