<?php declare(strict_types=1);

namespace Novuso\Common\Domain\Specification;

/**
 * Class OrSpecification
 */
class OrSpecification extends CompositeSpecification
{
    /**
     * First specification
     *
     * @var Specification
     */
    protected $firstSpec;

    /**
     * Second specification
     *
     * @var Specification
     */
    protected $secondSpec;

    /**
     * Constructs OrSpecification
     *
     * @param Specification $firstSpec  The first specification
     * @param Specification $secondSpec The second specification
     */
    public function __construct(Specification $firstSpec, Specification $secondSpec)
    {
        $this->firstSpec = $firstSpec;
        $this->secondSpec = $secondSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $this->firstSpec->isSatisfiedBy($candidate) || $this->secondSpec->isSatisfiedBy($candidate);
    }
}
