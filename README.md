# SortedLinkedList

A linked list that keeps values sorted. Holds `int` or `string` values, but not both.

## Requirements

- PHP 8.3+

## Usage

```php
$list = SortedLinkedList::ofIntegers();

$list->add(5);
$list->add(1);
$list->add(3);

$list->toArray();      // [1, 3, 5]
$list->contains(3);    // true
$list->remove(3);      // true
count($list);           // 2

foreach ($list as $value) { /* ... */ }
```

Adding a wrong type throws `TypeMismatchException`.

## Quality checks

```bash
make check   # CS + PHPStan (level max) + PHPUnit
```

Test coverage: 100% (classes, methods, lines).
