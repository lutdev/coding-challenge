<?php
declare(strict_types=1);

namespace App\Command;

use App\Application\Assertions\RoverCoordinatesIsValidAssertion;
use App\Application\Assertions\TopRightCoordinatesIsValidAssertion;
use App\Application\Enum\Direction;
use App\Application\Exceptions\InvalidRoverInformationException;
use App\Application\Service\NasaRoverService;
use App\Command\NasaRovers\Dto\BorderCoordinatesDto;
use App\Command\NasaRovers\Dto\RoverInformationDto;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class NasaRoversCommand extends Command
{
    private const LOWER_LEFT_X_COORDINATE = 0;
    private const LOWER_LEFT_Y_COORDINATE = 0;

    protected static $defaultName = 'app:nasa-rovers';
    protected static $defaultDescription = 'Command for navigate NASA rovers on the Mars';

    private QuestionHelper $questionHelper;

    public function __construct(
        private TopRightCoordinatesIsValidAssertion $topRightCoordinatesIsValidAssertion,
        private RoverCoordinatesIsValidAssertion $roverCoordinatesIsValidAssertion,
        private NasaRoverService $nasaRoverService,
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

    /**
     * @throws Exception
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->questionHelper = $this->getHelper('question');

        #region Get top-right coordinates
        $topRightCoordinates = $this->getTopRightCoordinates($input, $output);

        [$topRightXCoordinate, $topRightYCoordinate] = explode(' ', $topRightCoordinates);

        $this->topRightCoordinatesIsValidAssertion->assert(
            self::LOWER_LEFT_X_COORDINATE,
            self::LOWER_LEFT_Y_COORDINATE,
            (int)$topRightXCoordinate,
            (int)$topRightYCoordinate
        );

        $borderCoordinatesDto = new BorderCoordinatesDto(
            self::LOWER_LEFT_X_COORDINATE,
            self::LOWER_LEFT_Y_COORDINATE,
            (int)$topRightXCoordinate,
            (int)$topRightYCoordinate
        );
        #endregion

        $roversInformation = $this->getRoversInformation($input, $output);

        try {
            $rovers = $this->setupRovers($borderCoordinatesDto, $roversInformation);
        } catch (InvalidRoverInformationException $exception) {
            $output->write(
                \sprintf(
                    '<error>%s. Error in the line #%d</error>',
                    $exception->getMessage(),
                    $exception->getLineNumber()
                )
            );

            return Command::INVALID;
        }

        if (\count($rovers) === 0) {
            $output->write('Rovers are empty');

            return Command::SUCCESS;
        }

        foreach ($rovers as $roverInformation) {
            $output->write('Moving rover...');
            $rover = $this->nasaRoverService->process($roverInformation);

            try {
                $this->roverCoordinatesIsValidAssertion->assert(
                    $borderCoordinatesDto,
                    $rover->getXCoordinate(),
                    $rover->getYCoordinate()
                );
            } catch (InvalidArgumentException) {
                $output->writeln([
                    "\n",
                    \sprintf(
                        'Rover left Mars: %d %d %s',
                        $rover->getXCoordinate(),
                        $rover->getYCoordinate(),
                        $rover->getDirection()->toString()
                    ),
                    "\n"
                ]);
            }

            $output->writeln([
                "\n",
                \sprintf(
                    'Output: %d %d %s',
                    $rover->getXCoordinate(),
                    $rover->getYCoordinate(),
                    $rover->getDirection()->toString()
                ),
                "\n"
            ]);
        }

        return Command::SUCCESS;
    }

    private function getTopRightCoordinates(
        InputInterface $input,
        OutputInterface $output,
    ): string {
        $question = new Question('Enter top-right coordinates (x y): ');
        $question->setValidator(function ($answer) {
            if (!is_string($answer) || empty($answer) || 1 !== preg_match('/^[0-9]{1,} [0-9]{1,}$/', $answer)) {
                throw new \RuntimeException(
                    'Coordinates should be like X Y. For example, 0 0'
                );
            }

            return $answer;
        });

        return $this->questionHelper->ask($input, $output, $question);
    }

    /**
     * @return string[]
     */
    private function getRoversInformation(InputInterface $input, OutputInterface $output): array
    {
        $question = new Question('Enter rovers information: ');
        $question->setMultiline(true);

        $answer = $this->questionHelper->ask($input, $output, $question);

        $roversInformation = \explode("\n", $answer);
        $countLines = \count($roversInformation);

        if ($countLines%2 !== 0) {
            throw new InvalidArgumentException(
                'Every rover should have 2 lines: one with coordinates and one with actions. You have %d lines',
                $countLines
            );
        }

        return $roversInformation;
    }

    /**
     * @param string[] $roversInformation
     * @return RoverInformationDto[]
     */
    private function setupRovers(
        BorderCoordinatesDto $borderCoordinatesDto,
        array $roversInformation
    ): array {
        /** @var RoverInformationDto[] $rovers */
        $rovers = [];

        foreach ($roversInformation as $lineNumber => $information) {
            if ($this->isInformationAboutRoverPosition($information)) {
                [$roverXCoordinate, $roverYCoordinate, $direction] = explode(' ', $information);

                try {
                    $this->roverCoordinatesIsValidAssertion->assert(
                        $borderCoordinatesDto,
                        (int)$roverXCoordinate,
                        (int)$roverYCoordinate
                    );
                } catch (InvalidArgumentException $exception) {
                    throw new InvalidRoverInformationException(
                        $exception->getMessage(),
                        $lineNumber
                    );
                }

                $roverDirectionEnum = match (strtoupper($direction)) {
                    'N' => Direction::North,
                    'S' => Direction::South,
                    'W' => Direction::West,
                    'E' => Direction::East,
                    default => throw new InvalidRoverInformationException(
                        'Invalid direction. It can be one of the: N, S, W, E',
                        $lineNumber
                    )
                };

                $rovers[$lineNumber] = new RoverInformationDto(
                    (int)$roverXCoordinate,
                    (int)$roverYCoordinate,
                    $roverDirectionEnum
                );
            } else if ($this->isInformationAboutRoverActions($information)) {
                $roverCoordinatesLineNumber = $lineNumber - 1;

                $rovers[$roverCoordinatesLineNumber]->setActions($information);
            } else {
                throw new InvalidRoverInformationException(
                    'Invalid rover information',
                    $lineNumber
                );
            }
        }

        return $rovers;
    }

    private function isInformationAboutRoverPosition(string $roverInformation): bool
    {
        return 1 === preg_match('/^[0-9]{1,} [0-9]{1,} [NSWE]$/', $roverInformation);
    }

    private function isInformationAboutRoverActions(string $roverInformation): bool
    {
        return 1 === preg_match('/^[MLR]{1,}$/', $roverInformation);
    }
}