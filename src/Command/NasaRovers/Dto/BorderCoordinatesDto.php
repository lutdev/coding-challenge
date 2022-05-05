<?php
declare(strict_types=1);

namespace App\Command\NasaRovers\Dto;

class BorderCoordinatesDto
{
    public function __construct(
        private int $minXCoordinate,
        private int $minYCoordinate,
        private int $maxXCoordinate,
        private int $maxYCoordinate
    ) {
    }

    public function getMinXCoordinate(): int
    {
        return $this->minXCoordinate;
    }

    public function getMinYCoordinate(): int
    {
        return $this->minYCoordinate;
    }

    public function getMaxXCoordinate(): int
    {
        return $this->maxXCoordinate;
    }

    public function getMaxYCoordinate(): int
    {
        return $this->maxYCoordinate;
    }
}
