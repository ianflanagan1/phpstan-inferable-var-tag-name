<?php

declare(strict_types=1);

namespace IanFlanagan\PhpstanRules\InferableVarTagName\Tests;

use IanFlanagan\PhpstanRules\InferableVarTagName\InferableVarTagNameRule;
use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\FileTypeMapper;

/**
 * @extends RuleTestCase<InferableVarTagNameRule>
 */
class InferableVarTagNameRuleTest extends RuleTestCase
{
    protected const string TIP = 'Name the variable only where it cannot be inferred, such as above foreach or destructuring.';

    public function test_a_var_tag_naming_the_assigned_variable_is_reported_whether_assigned_by_value_or_reference(): void
    {
        $this->analyse([__DIR__ . '/data/assigned-variable.php'], [
            [self::message('ids'), 9, self::TIP],
            [self::message('byReference'), 12, self::TIP],
        ]);
    }

    public function test_a_var_tag_naming_the_returned_variable_is_reported(): void
    {
        $this->analyse([__DIR__ . '/data/returned-variable.php'], [
            [self::message('value'), 10, self::TIP],
        ]);
    }

    public function test_a_var_tag_naming_the_static_variable_is_reported(): void
    {
        $this->analyse([__DIR__ . '/data/static-variable.php'], [
            [self::message('seen'), 9, self::TIP],
        ]);
    }

    public function test_a_var_tag_naming_its_property_is_reported(): void
    {
        $this->analyse([__DIR__ . '/data/property.php'], [
            [self::message('ids'), 9, self::TIP],
        ]);
    }

    public function test_a_var_tag_naming_its_promoted_property_is_reported_but_not_one_on_a_plain_parameter(): void
    {
        $this->analyse([__DIR__ . '/data/promoted-property.php'], [
            [self::message('ids'), 10, self::TIP],
        ]);
    }

    public function test_phpstan_and_psalm_prefixed_var_tags_are_reported_like_var(): void
    {
        $this->analyse([__DIR__ . '/data/prefixed-tags.php'], [
            [self::message('ids'), 9, self::TIP],
            [self::message('names'), 12, self::TIP],
        ]);
    }

    public function test_var_tags_inside_closures_and_traits_are_reported(): void
    {
        $this->analyse([__DIR__ . '/data/closure-and-trait.php'], [
            [self::message('ids'), 9, self::TIP],
            [self::message('ids'), 21, self::TIP],
        ]);
    }

    public function test_a_var_tag_keeps_its_name_where_the_target_cannot_be_inferred(): void
    {
        $this->analyse([__DIR__ . '/data/uninferable.php'], []);
    }

    public function test_a_var_tag_without_a_name_is_allowed(): void
    {
        $this->analyse([__DIR__ . '/data/nameless.php'], []);
    }

    #[Override]
    protected function getRule(): Rule
    {
        return new InferableVarTagNameRule(self::getContainer()->getByType(FileTypeMapper::class));
    }

    protected static function message(string $name): string
    {
        return "PHPDoc tag @var names \$$name, which PHPStan infers here; remove the name.";
    }
}
