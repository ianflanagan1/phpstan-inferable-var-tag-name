<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName\Tests\data\Uninferable;

/** @param list<mixed> $rows */
function walk(array $rows, mixed $value): mixed
{
    /** @var int $row */
    foreach ($rows as $row) {
    }

    /** @var int $first */
    [$first, $second] = $rows;

    /** @var int $value */
    $other = $value;

    /**
     * @var int $value
     * @var int $other
     */
    $other = $value;

    /** @var int $value */
    return $value + 1;
}

function shared(mixed $value): void
{
    /** @var list<int> $a */
    static $a = [], $b = [];

    $name = 'dynamic';

    /** @var int $dynamic */
    $$name = $value;
}

final class Pair
{
    /** @var int $left */
    public int $left = 0, $right = 0;
}
