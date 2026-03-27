<?php

declare(strict_types=1);

namespace Piotr\SortedLinkedList;

use ArrayIterator;

class SortedLinkedList implements SortedLinkedListInterface
{
    private ?Node $head = null;

    /** @var non-negative-int */
    private int $count = 0;

    private function __construct(private ValueType $type) {}

    public static function ofIntegers(): self
    {
        return new self(ValueType::Integer);
    }

    public static function ofStrings(): self
    {
        return new self(ValueType::String);
    }

    public function add(int|string $value): void
    {
        $this->assertTypeMatches($value);

        $newNode = new Node($value);

        if (null === $this->head                // no head yet
            || $value < $this->head->value      // or the element must be first
        ) {
            $newNode->next = $this->head;       // attach to list start
            $this->head = $newNode;             // update head
        } else {
            $current = $this->head;
            while (null !== $current->next && $current->next->value <= $value) {
                $current = $current->next;
            }

            // the right spot is after $current
            $newNode->next = $current->next;
            $current->next = $newNode;
        }

        $this->count++;
    }

    public function remove(int|string $value): bool
    {
        $this->assertTypeMatches($value);

        if (null === $this->head) {              // empty list
            return false;
        }

        if ($this->head->value === $value) {    // removing head
            $removed = $this->head;
            $this->head = $removed->next;
            unset($removed);                    // free memory eagerly
            $this->decrementCount();
            return true;
        }

        $current = $this->head;
        while (null !== $current->next && $current->next->value < $value) {
            $current = $current->next;          // leverage sort order for early exit
        }

        if (null === $current->next || $current->next->value !== $value) {
            return false;                       // not found
        }

        $removed = $current->next;
        $current->next = $removed->next;        // unlink the node
        unset($removed);                        // free memory eagerly
        $this->decrementCount();
        return true;
    }

    public function contains(int|string $value): bool
    {
        $this->assertTypeMatches($value);

        $current = $this->head;
        while (null !== $current && $current->value <= $value) {
            if ($current->value === $value) {
                return true;
            }
            $current = $current->next;
        }

        return false;
    }

    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return list<int|string>
     */
    public function toArray(): array
    {
        $values = [];
        $current = $this->head;
        while (null !== $current) {
            $values[] = $current->value;
            $current = $current->next;
        }

        return $values;
    }

    /**
     * @return ArrayIterator<int, int|string>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    private function decrementCount(): void
    {
        if (0 < $this->count) {
            $this->count--;
        }
    }

    private function assertTypeMatches(int|string $value): void
    {
        if (!$this->type->matches($value)) {
            throw TypeMismatchException::create($this->type, $value);
        }
    }
}
