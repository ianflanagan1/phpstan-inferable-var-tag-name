<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName\Tests\data\AssignedVariable;

function load(mixed $value): void
{
    /** @var list<int> $ids */
    $ids = $value;

    /** @var list<int> $byReference */
    $byReference = &$value;
}
