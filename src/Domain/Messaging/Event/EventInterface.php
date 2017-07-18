<?php declare(strict_types=1);

namespace Novuso\Common\Domain\Messaging\Event;

use Novuso\Common\Domain\Messaging\PayloadInterface;
use Novuso\System\Exception\DomainException;

/**
 * EventInterface is the interface for a domain event
 *
 * @copyright Copyright (c) 2017, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
interface EventInterface extends PayloadInterface
{
    /**
     * Creates instance from array representation
     *
     * @param array $data The array representation
     *
     * @return EventInterface
     *
     * @throws DomainException When data is not valid
     */
    public static function fromArray(array $data);

    /**
     * Retrieves an array representation
     *
     * @return array
     */
    public function toArray(): array;
}
