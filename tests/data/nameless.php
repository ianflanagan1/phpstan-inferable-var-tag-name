<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName\Tests\data\Nameless;

final class Holder
{
    /** @var list<int> */
    public array $ids = [];

    /** @return list<int> */
    public function load(mixed $value): array
    {
        /** @var list<int> */
        $ids = $value;

        /** @var list<int> */
        return $ids;
    }
}
