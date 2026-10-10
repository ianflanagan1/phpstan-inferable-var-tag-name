<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName\Tests\data\PromotedProperty;

final class Holder
{
    public function __construct(
        /** @var list<int> $ids */
        public array $ids,
        /** @var list<int> $plain */
        array $plain,
    ) {
    }
}
