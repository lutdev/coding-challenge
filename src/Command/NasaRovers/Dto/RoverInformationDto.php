<?php
declare(strict_types=1);

namespace App\Command\NasaRovers\Dto;

use App\Application\Enum\Direction;

class RoverInformationDto
{
    private string $actions = '';

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

    public function getActions(): string
    {
        return $this->actions;
    }

    public function setActions(string $actions): void
    {
        $this->actions = $actions;
    }
}
