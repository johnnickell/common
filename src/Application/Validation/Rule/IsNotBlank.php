<?php declare(strict_types=1);

namespace Novuso\Common\Application\Validation\Rule;

use Novuso\Common\Domain\Specification\CompositeSpecification;
use Novuso\System\Utility\Validate;

/**
 * Class IsNotBlank
 */
class IsNotBlank extends CompositeSpecification
{
    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return Validate::isNotBlank($candidate);
    }
}
