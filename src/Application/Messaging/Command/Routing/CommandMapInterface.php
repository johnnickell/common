<?php declare(strict_types=1);

namespace Novuso\Common\Application\Messaging\Command\Routing;

use Novuso\Common\Domain\Messaging\Command\CommandHandlerInterface;
use Novuso\System\Exception\LookupException;

/**
 * CommandMapInterface is the interface for a command map
 *
 * @copyright Copyright (c) 2017, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
interface CommandMapInterface
{
    /**
     * Retrieves handler by command class name
     *
     * @param string $commandClass The full command class name
     *
     * @return CommandHandlerInterface
     *
     * @throws LookupException When a handler is not registered
     */
    public function getHandler(string $commandClass): CommandHandlerInterface;

    /**
     * Checks if a handler is defined for a command
     *
     * @param string $commandClass The full command class name
     *
     * @return bool
     */
    public function hasHandler(string $commandClass): bool;
}
