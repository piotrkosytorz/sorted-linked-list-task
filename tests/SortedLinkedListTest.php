<?php

declare(strict_types=1);

namespace Piotr\SortedLinkedList\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Piotr\SortedLinkedList\SortedLinkedList;
use Piotr\SortedLinkedList\SortedLinkedListInterface;
use Piotr\SortedLinkedList\TypeMismatchException;

final class SortedLinkedListTest extends TestCase
{
    // --- Factory methods ---

    #[Test]
    public function ofIntegersCreatesIntegerList(): void
    {
        self::assertInstanceOf(SortedLinkedListInterface::class, SortedLinkedList::ofIntegers());
    }

    #[Test]
    public function ofStringsCreatesStringList(): void
    {
        self::assertInstanceOf(SortedLinkedListInterface::class, SortedLinkedList::ofStrings());
    }

    // --- Empty list ---

    #[Test]
    public function emptyIntegerList(): void
    {
        $list = SortedLinkedList::ofIntegers();

        self::assertSame(0, count($list));
        self::assertSame([], iterator_to_array($list));
    }

    #[Test]
    public function emptyStringList(): void
    {
        $list = SortedLinkedList::ofStrings();

        self::assertSame(0, count($list));
        self::assertSame([], iterator_to_array($list));
    }

    // --- add() ---

    /**
     * @param list<int|string> $input
     * @param list<int|string> $expected
     */
    #[Test]
    #[DataProvider('sortedInsertionProvider')]
    public function addKeepsValuesSorted(SortedLinkedList $list, array $input, array $expected): void
    {
        foreach ($input as $value) {
            $list->add($value);
        }

        self::assertSame($expected, iterator_to_array($list));
        self::assertSame(count($expected), count($list));
    }

    /**
     * @return iterable<string, array{SortedLinkedList, list<int|string>, list<int|string>}>
     */
    public static function sortedInsertionProvider(): iterable
    {
        yield 'integers unsorted' => [SortedLinkedList::ofIntegers(), [5, 1, 3, 2, 4], [1, 2, 3, 4, 5]];
        yield 'integers already sorted' => [SortedLinkedList::ofIntegers(), [1, 2, 3], [1, 2, 3]];
        yield 'integers reverse sorted' => [SortedLinkedList::ofIntegers(), [3, 2, 1], [1, 2, 3]];
        yield 'integers with negatives' => [SortedLinkedList::ofIntegers(), [-5, 3, -1, 0], [-5, -1, 0, 3]];
        yield 'integers with duplicates' => [SortedLinkedList::ofIntegers(), [2, 1, 2, 2], [1, 2, 2, 2]];
        yield 'single integer' => [SortedLinkedList::ofIntegers(), [42], [42]];
        yield 'strings' => [SortedLinkedList::ofStrings(), ['cherry', 'apple', 'banana'], ['apple', 'banana', 'cherry']];
        yield 'strings case sensitive' => [SortedLinkedList::ofStrings(), ['banana', 'Apple', 'cherry'], ['Apple', 'banana', 'cherry']];
        yield 'strings with empty' => [SortedLinkedList::ofStrings(), ['', 'a', ''], ['', '', 'a']];
        yield 'strings with duplicates' => [SortedLinkedList::ofStrings(), ['b', 'a', 'b'], ['a', 'b', 'b']];
    }

    // --- remove() ---

    /**
     * @param list<int|string> $input
     * @param list<int|string> $expectedList
     */
    #[Test]
    #[DataProvider('removeProvider')]
    public function removeValue(SortedLinkedList $list, array $input, int|string $toRemove, bool $expectedResult, array $expectedList): void
    {
        foreach ($input as $value) {
            $list->add($value);
        }

        self::assertSame($expectedResult, $list->remove($toRemove));
        self::assertSame($expectedList, iterator_to_array($list));
        self::assertSame(count($expectedList), count($list));
    }

    /**
     * @return iterable<string, array{SortedLinkedList, list<int|string>, int|string, bool, list<int|string>}>
     */
    public static function removeProvider(): iterable
    {
        yield 'remove middle' => [SortedLinkedList::ofIntegers(), [1, 2, 3], 2, true, [1, 3]];
        yield 'remove head' => [SortedLinkedList::ofIntegers(), [1, 2, 3], 1, true, [2, 3]];
        yield 'remove tail' => [SortedLinkedList::ofIntegers(), [1, 2, 3], 3, true, [1, 2]];
        yield 'remove only element' => [SortedLinkedList::ofIntegers(), [1], 1, true, []];
        yield 'remove non-existent' => [SortedLinkedList::ofIntegers(), [1, 3], 2, false, [1, 3]];
        yield 'remove from empty' => [SortedLinkedList::ofIntegers(), [], 1, false, []];
        yield 'remove first duplicate only' => [SortedLinkedList::ofIntegers(), [2, 2, 2], 2, true, [2, 2]];
        yield 'remove string' => [SortedLinkedList::ofStrings(), ['a', 'b', 'c'], 'b', true, ['a', 'c']];
        yield 'remove non-existent string' => [SortedLinkedList::ofStrings(), ['a', 'c'], 'b', false, ['a', 'c']];
    }

    // --- contains() ---

    /**
     * @param list<int|string> $input
     */
    #[Test]
    #[DataProvider('containsProvider')]
    public function containsValue(SortedLinkedList $list, array $input, int|string $search, bool $expected): void
    {
        foreach ($input as $value) {
            $list->add($value);
        }

        self::assertSame($expected, $list->contains($search));
    }

    /**
     * @return iterable<string, array{SortedLinkedList, list<int|string>, int|string, bool}>
     */
    public static function containsProvider(): iterable
    {
        yield 'found at head' => [SortedLinkedList::ofIntegers(), [1, 3, 5], 1, true];
        yield 'found at tail' => [SortedLinkedList::ofIntegers(), [1, 3, 5], 5, true];
        yield 'found in middle' => [SortedLinkedList::ofIntegers(), [1, 3, 5], 3, true];
        yield 'not found (between values)' => [SortedLinkedList::ofIntegers(), [1, 5, 10], 3, false];
        yield 'not found (beyond tail)' => [SortedLinkedList::ofIntegers(), [1, 3], 5, false];
        yield 'empty list' => [SortedLinkedList::ofIntegers(), [], 1, false];
        yield 'string found' => [SortedLinkedList::ofStrings(), ['a', 'b', 'c'], 'b', true];
        yield 'string not found' => [SortedLinkedList::ofStrings(), ['a', 'c'], 'b', false];
    }

    // --- count() ---

    #[Test]
    public function countTracksAdditionsAndRemovals(): void
    {
        $list = SortedLinkedList::ofIntegers();

        self::assertSame(0, count($list));

        $list->add(1);
        $list->add(2);
        self::assertSame(2, count($list));

        $list->remove(1);
        self::assertSame(1, count($list));
    }

    // --- Type mismatch ---

    #[Test]
    public function addThrowsOnTypeMismatchForIntegerList(): void
    {
        $this->expectException(TypeMismatchException::class);
        SortedLinkedList::ofIntegers()->add('string');
    }

    #[Test]
    public function addThrowsOnTypeMismatchForStringList(): void
    {
        $this->expectException(TypeMismatchException::class);
        SortedLinkedList::ofStrings()->add(1);
    }

    #[Test]
    public function removeThrowsOnTypeMismatchForIntegerList(): void
    {
        $this->expectException(TypeMismatchException::class);
        SortedLinkedList::ofIntegers()->remove('string');
    }

    #[Test]
    public function removeThrowsOnTypeMismatchForStringList(): void
    {
        $this->expectException(TypeMismatchException::class);
        SortedLinkedList::ofStrings()->remove(1);
    }

    #[Test]
    public function containsThrowsOnTypeMismatchForIntegerList(): void
    {
        $this->expectException(TypeMismatchException::class);
        SortedLinkedList::ofIntegers()->contains('string');
    }

    #[Test]
    public function containsThrowsOnTypeMismatchForStringList(): void
    {
        $this->expectException(TypeMismatchException::class);
        SortedLinkedList::ofStrings()->contains(1);
    }

}
