<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName\Tests\data\StaticVariable;

function remember(): void
{
    /** @var list<int> $seen */
    static $seen = [];
}
