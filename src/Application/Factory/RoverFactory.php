<?php
declare(strict_types=1);

namespace App\Application\Factory;

use App\Application\Enum\Direction;
use App\Domain\Entity\Rover;

class RoverFactory
{
    public function create(
        int $roverXPosition,
        int $roverYPosition,
        Direction $roverDirectionEnum,
    ): Rover {
        return new Rover($roverXPosition, $roverYPosition, $roverDirectionEnum);
    }
}
