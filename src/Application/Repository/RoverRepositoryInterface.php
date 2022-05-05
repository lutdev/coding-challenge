<?php
declare(strict_types=1);

namespace App\Application\Repository;

use App\Domain\Entity\Rover;

interface RoverRepositoryInterface
{
    public function turnLeft(Rover $rover): void;

    public function turnRight(Rover $rover): void;

    public function move(Rover $rover): void;
}