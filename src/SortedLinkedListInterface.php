<?php

declare(strict_types=1);

namespace Piotr\SortedLinkedList;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<int, int|string>
 */
interface SortedLinkedListInterface extends Countable, IteratorAggregate
{
    public function add(int|string $value): void;

    public function remove(int|string $value): bool;

    public function contains(int|string $value): bool;

    /**
     * @return list<int|string>
     */
    public function toArray(): array;
}
