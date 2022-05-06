<?php
declare(strict_types=1);

namespace App\Application\Infrastructure;

use App\Application\Enum\Direction;
use App\Application\Repository\RoverRepositoryInterface;
use App\Domain\Entity\Rover;

final class RoverRepository implements RoverRepositoryInterface
{
    public function turnLeft(Rover $rover): Rover
    {
        match ($rover->getDirection()) {
            Direction::North => $rover->setDirection(Direction::West),
            Direction::South => $rover->setDirection(Direction::East),
            Direction::West => $rover->setDirection(Direction::South),
            Direction::East => $rover->setDirection(Direction::North),
        };

        return $rover;
    }

    public function turnRight(Rover $rover): Rover
    {
        match ($rover->getDirection()) {
            Direction::North => $rover->setDirection(Direction::East),
            Direction::South => $rover->setDirection(Direction::West),
            Direction::West => $rover->setDirection(Direction::North),
            Direction::East => $rover->setDirection(Direction::South),
        };

        return $rover;
    }

    public function move(Rover $rover): Rover
    {
        match ($rover->getDirection()) {
            Direction::North => $rover->setCoordinateY($rover->getCoordinateY() + 1),
            Direction::South => $rover->setCoordinateY($rover->getCoordinateY() - 1),
            Direction::West => $rover->setCoordinateX($rover->getCoordinateX() - 1),
            Direction::East => $rover->setCoordinateX($rover->getCoordinateX() + 1),
        };

        return $rover;
    }
}