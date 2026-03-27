<?php

declare(strict_types=1);

namespace Piotr\SortedLinkedList;

/**
 * @internal
 */
final class Node
{
    public function __construct(
        public readonly int|string $value,
        public ?self $next = null,
    ) {}
}
