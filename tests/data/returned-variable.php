<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName\Tests\data\ReturnedVariable;

/** @return list<int> */
function load(mixed $value): array
{
    /** @var list<int> $value */
    return $value;
}
