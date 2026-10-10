# phpstan-inferable-var-tag-name

A PHPStan rule that reports a variable name in a `@var` tag (also `@phpstan-var` and `@psalm-var`) where PHPStan infers the target without it.

## Installation

```sh
composer require --dev ianflanagan1/phpstan-inferable-var-tag-name
```

With [phpstan/extension-installer](https://github.com/phpstan/extension-installer) the rule is enabled automatically. Otherwise, include it in your `phpstan.neon`:

```neon
includes:
    - vendor/ianflanagan1/phpstan-inferable-var-tag-name/rules.neon
```

Requires PHP 8.3+ and PHPStan 2.1+.

## What it reports

```php
/** @var list<int> $ids */   // reported
$ids = $repository->ids();

/** @var list<int> */        // preferred
$ids = $repository->ids();
```

PHPStan infers the target for:

- the variable assigned, by value or by reference
- the variable returned
- a single `static` variable
- a single property, including a promoted constructor property

The name is still required, and not reported, above `foreach`, destructuring, a statement declaring several variables or properties, or a docblock with several `@var` tags.

Identifier: `varTag.inferableName`. To ignore one occurrence, add `// @phpstan-ignore varTag.inferableName` to the line.

## Development

```sh
composer install
vendor/bin/phpunit
vendor/bin/phpstan analyse
vendor/bin/pint --test
```

The test in `tests/` is built on PHPStan's `RuleTestCase`, with fixture files in `tests/data/`. The fixtures break the rule on purpose, so PHPStan and Pint skip them.

## License

MIT
