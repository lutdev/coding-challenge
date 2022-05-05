<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Application\Enum\Direction;

class Rover
{
    public function __construct(
        private int $coordinateX,
        private int $coordinateY,
        private Direction $direction
    ) {
    }

    public function getCoordinateX(): int
    {
        return $this->coordinateX;
    }

    public function getCoordinateY(): int
    {
        return $this->coordinateY;
    }

    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function setCoordinateX(int $coordinateX): void
    {
        $this->coordinateX = $coordinateX;
    }

    public function setCoordinateY(int $coordinateY): void
    {
        $this->coordinateY = $coordinateY;
    }

    public function setDirection(Direction $direction): void
    {
        $this->direction = $direction;
    }
}
