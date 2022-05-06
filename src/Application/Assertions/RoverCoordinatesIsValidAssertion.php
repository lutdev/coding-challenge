<?php
declare(strict_types=1);

namespace App\Application\Assertions;

use App\Command\NasaRovers\Dto\BorderCoordinatesDto;
use InvalidArgumentException;

class RoverCoordinatesIsValidAssertion
{
    /**
     * @throws InvalidArgumentException
     */
    public function assert(
        BorderCoordinatesDto $borderCoordinatesDto,
        int $roverXCoordinate,
        int $roverYCoordinate
    ): void {
        if ($roverXCoordinate < $borderCoordinatesDto->getMinXCoordinate()) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Rover X coordinate should be more than %d',
                    $borderCoordinatesDto->getMinXCoordinate()
                )
            );
        }

        if ($roverXCoordinate > $borderCoordinatesDto->getMaxXCoordinate()) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Rover X coordinate should be less than %d',
                    $borderCoordinatesDto->getMaxXCoordinate()
                )
            );
        }

        if ($roverYCoordinate < $borderCoordinatesDto->getMinYCoordinate()) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Rover Y coordinate should be more than %d',
                    $borderCoordinatesDto->getMinYCoordinate()
                )
            );
        }

        if ($roverYCoordinate > $borderCoordinatesDto->getMaxYCoordinate()) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Rover Y coordinate should be less than %d',
                    $borderCoordinatesDto->getMaxYCoordinate()
                )
            );
        }
    }
}
