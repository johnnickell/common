<?php declare(strict_types=1);

namespace Novuso\Common\Domain\Type;

use function Novuso\Common\Functions\{
    bind,
    same_type
};

/**
 * ValueObject is the base class for domain value objects
 *
 * Implementations must adhere to value characteristics:
 *
 * * It is maintained as immutable
 * * It measures, quantifies, or describes a thing in the domain
 * * It models a conceptual whole by composing related attributes as a unit
 * * It is completely replaceable when the measurement or description changes
 * * It can be compared with others using value equality
 * * It supplies its collaborators with side-effect-free behavior
 *
 * @copyright Copyright (c) 2017, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
abstract class ValueObject implements Value
{
    /**
     * {@inheritdoc}
     */
    abstract public function toString(): string;

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return call_user_func(bind(function () {
            return serialize(get_object_vars($this));
        }, $this));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        call_user_func(bind(function () use ($serialized) {
            $properties = unserialize($serialized);
            foreach ($properties as $property => $value) {
                $this->$property = $value;
            }
        }, $this));
    }

    /**
     * {@inheritdoc}
     */
    public function equals($object): bool
    {
        if ($this === $object) {
            return true;
        }

        if (!same_type($this, $object)) {
            return false;
        }

        return $this->toString() === $object->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function hashValue(): string
    {
        return $this->toString();
    }
}
