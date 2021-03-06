<?php declare(strict_types=1);

namespace Novuso\Common\Test\Application\Validation\Rule;

use Novuso\Common\Application\Validation\Rule\CountRange;
use Novuso\System\Test\TestCase\UnitTestCase;

/**
 * @covers \Novuso\Common\Application\Validation\Rule\CountRange
 */
class CountRangeTest extends UnitTestCase
{
    public function test_that_is_satisfied_by_returns_true_when_validation_passes()
    {
        $rule = new CountRange(1, 3);

        $this->assertTrue($rule->isSatisfiedBy(['foo', 'bar', 'baz']));
    }

    public function test_that_is_satisfied_by_returns_false_when_validation_fails()
    {
        $rule = new CountRange(1, 3);

        $this->assertFalse($rule->isSatisfiedBy([]));
    }
}
