<?php

declare(strict_types=1);

namespace Piotr\SortedLinkedList;

use function is_int;
use function is_string;

enum ValueType: string
{
    case Integer = 'int';
    case String = 'string';

    public function matches(int|string $value): bool
    {
        return match ($this) {
            self::Integer => is_int($value),
            self::String => is_string($value),
        };
    }
}
