<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName\Tests\data\ClosureAndTrait;

trait HoldsIds
{
    /** @var list<int> $ids */
    public array $ids = [];
}

final class Holder
{
    use HoldsIds;
}

function make(): callable
{
    return function (mixed $value): void {
        /** @var list<int> $ids */
        $ids = $value;
    };
}
