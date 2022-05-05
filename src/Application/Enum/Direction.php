<?php
declare(strict_types=1);

namespace App\Application\Enum;

enum Direction
{
    case North;
    case South;
    case West;
    case East;

    public function toString(): string
    {
        return match ($this) {
            self::North => 'N',
            self::South => 'S',
            self::West => 'W',
            self::East => 'E',
        };
    }
}
