<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName;

use Override;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\FileTypeMapper;

/**
 * Reports a variable name in a @var tag where PHPStan can infer the target without it, so:
 *
 * ```php
 * /** @var Foo $foo *\/
 * $foo = ...;
 * ```
 *
 * should be written:
 *
 * ```php
 * /** @var Foo *\/
 * $foo = ...;
 * ```
 *
 * Inferred targets: the variable assigned (by value or reference), the variable returned,
 * a single static variable, a single property, and a promoted constructor property. Anything else
 * (foreach, destructuring, a tag naming some other variable, a docblock with several @var tags)
 * keeps its name.
 *
 * @implements Rule<Stmt>
 */
final readonly class InferableVarTagNameRule implements Rule
{
    public function __construct(
        private FileTypeMapper $fileTypeMapper,
    ) {
    }

    #[Override]
    public function getNodeType(): string
    {
        return Stmt::class;
    }

    #[Override]
    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];

        foreach (self::inferredTargets($node) as [$documented, $name]) {
            $error = $this->check($documented, $name, $scope);

            if ($error !== null) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * The nodes in $stmt that may carry a @var docblock, each paired with the name PHPStan infers for it
     *
     * @return list<array{Node, string}>
     */
    private static function inferredTargets(Stmt $stmt): array
    {
        $name = match (true) {
            $stmt instanceof Stmt\Expression && ($stmt->expr instanceof Assign || $stmt->expr instanceof AssignRef)
                => self::variableName($stmt->expr->var),
            $stmt instanceof Stmt\Return_
                => self::variableName($stmt->expr),
            $stmt instanceof Stmt\Static_ && count($stmt->vars) === 1
                => self::variableName($stmt->vars[0]->var),
            $stmt instanceof Stmt\Property && count($stmt->props) === 1
                => $stmt->props[0]->name->toString(),
            default => null,
        };

        if ($name !== null) {
            return [[$stmt, $name]];
        }

        if (!$stmt instanceof Stmt\ClassMethod) {
            return [];
        }

        $targets = [];

        foreach ($stmt->params as $param) {
            $name = self::variableName($param->var);

            if ($param->isPromoted() && $name !== null) {
                $targets[] = [$param, $name];
            }
        }

        return $targets;
    }

    private static function variableName(?Expr $expr): ?string
    {
        return $expr instanceof Variable && is_string($expr->name)
            ? $expr->name
            : null;
    }

    private function check(Node $documented, string $name, Scope $scope): ?IdentifierRuleError
    {
        $docComment = $documented->getDocComment();

        if ($docComment === null) {
            return null;
        }

        $varTags = $this->fileTypeMapper->getResolvedPhpDoc(
            $scope->getFile(),
            $scope->getClassReflection()?->getName(),
            $scope->getTraitReflection()?->getName(),
            $scope->getFunctionName(),
            $docComment->getText(),
        )->getVarTags();

        if (count($varTags) !== 1 || !array_key_exists($name, $varTags)) {
            return null;
        }

        return RuleErrorBuilder::message(sprintf(
            'PHPDoc tag @var names $%s, which PHPStan infers here; remove the name.',
            $name,
        ))
            ->identifier('varTag.inferableName')
            ->line($docComment->getStartLine())
            ->tip('Name the variable only where it cannot be inferred, such as above foreach or destructuring.')
            ->build();
    }
}
