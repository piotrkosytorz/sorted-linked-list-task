<?php

declare(strict_types=1);

namespace Piotr\SortedLinkedList;

use function gettype;

use InvalidArgumentException;

use function sprintf;

final class TypeMismatchException extends InvalidArgumentException
{
    public static function create(ValueType $expected, int|string $actual): self
    {
        return new self(
            sprintf(
                'Invalid value type. Expected %s, got %s.',
                $expected->value,
                gettype($actual),
            ),
        );
    }
}
