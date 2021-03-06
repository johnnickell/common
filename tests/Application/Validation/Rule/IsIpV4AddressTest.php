<?php declare(strict_types=1);

namespace Novuso\Common\Test\Application\Validation\Rule;

use Novuso\Common\Application\Validation\Rule\IsIpV4Address;
use Novuso\System\Test\TestCase\UnitTestCase;

/**
 * @covers \Novuso\Common\Application\Validation\Rule\IsIpV4Address
 */
class IsIpV4AddressTest extends UnitTestCase
{
    public function test_that_is_satisfied_by_returns_true_when_validation_passes()
    {
        $rule = new IsIpV4Address();

        $this->assertTrue($rule->isSatisfiedBy('127.0.0.1'));
    }

    public function test_that_is_satisfied_by_returns_false_when_validation_fails()
    {
        $rule = new IsIpV4Address();

        $this->assertFalse($rule->isSatisfiedBy('127.0.0.0.1'));
    }
}
