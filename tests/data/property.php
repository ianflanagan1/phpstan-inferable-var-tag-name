<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName\Tests\data\Property;

final class Holder
{
    /** @var list<int> $ids */
    public array $ids = [];
}
