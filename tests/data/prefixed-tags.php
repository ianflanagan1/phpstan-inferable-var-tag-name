<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName\Tests\data\PrefixedTags;

function load(mixed $value): void
{
    /** @phpstan-var list<int> $ids */
    $ids = $value;

    /** @psalm-var list<int> $names */
    $names = $value;
}
