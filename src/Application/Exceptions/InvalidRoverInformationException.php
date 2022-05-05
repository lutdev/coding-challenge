<?php
declare(strict_types=1);

namespace App\Application\Exceptions;

use InvalidArgumentException;

class InvalidRoverInformationException extends InvalidArgumentException
{
    public function __construct(
        string $message,
        private int $lineNumber
    ) {
        parent::__construct($message);
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }
}
