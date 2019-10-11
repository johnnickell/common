<?php declare(strict_types=1);

namespace Novuso\Common\Application\Validation\Rule;

use Novuso\Common\Domain\Specification\CompositeSpecification;
use Novuso\System\Utility\Validate;

/**
 * Class IsDigits
 */
class IsDigits extends CompositeSpecification
{
    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return Validate::isDigits($candidate);
    }
}
