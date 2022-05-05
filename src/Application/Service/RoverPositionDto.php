<?php
declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Enum\Direction;

class RoverPositionDto
{
    public function __construct(
        private int $xCoordinate,
        private int $yCoordinate,
        private Direction $direction,
    ) {
    }

    public function getXCoordinate(): int
    {
        return $this->xCoordinate;
    }

    public function getYCoordinate(): int
    {
        return $this->yCoordinate;
    }

    public function getDirection(): Direction
    {
        return $this->direction;
    }
}
