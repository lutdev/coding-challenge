<?php
declare(strict_types=1);

namespace App\Application\Assertions;

use InvalidArgumentException;

class TopRightCoordinatesIsValidAssertion
{
    public function assert(
        int $lowerLeftXCoordinate,
        int $lowerLeftYCoordinate,
        int $topRightXCoordinate,
        int $topRightYCoordinate
    ): void {
        if ($topRightXCoordinate <= $lowerLeftXCoordinate) {
            throw new InvalidArgumentException(
                \sprintf('Top-right X coordinate should be more than %d', $lowerLeftXCoordinate)
            );
        }

        if ($topRightYCoordinate <= $lowerLeftYCoordinate) {
            throw new InvalidArgumentException(
                \sprintf('Top-right Y coordinate should be more than %d', $lowerLeftYCoordinate)
            );
        }
    }
}
