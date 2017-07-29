<?php declare(strict_types=1);

namespace Novuso\Common\Application\Messaging\Command\Routing;

use Novuso\Common\Domain\Messaging\Command\CommandHandlerInterface;
use Novuso\Common\Domain\Messaging\Command\CommandInterface;
use Novuso\System\Exception\DomainException;
use Novuso\System\Exception\LookupException;
use Novuso\System\Type\Type;
use Novuso\System\Utility\Validate;
use Psr\Container\ContainerInterface;

/**
 * ServiceAwareCommandMap is a command class to handler service map
 *
 * @copyright Copyright (c) 2017, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
class ServiceAwareCommandMap implements CommandMapInterface
{
    /**
     * Service container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Command handlers
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * Constructs ServiceAwareCommandMap
     *
     * @param ContainerInterface $container The service container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Registers command handlers
     *
     * The command to handler map must follow this format:
     * [
     *     SomeCommand::class => 'handler_service_name'
     * ]
     *
     * @param array $commandToHandlerMap A map of class names to service names
     *
     * @return void
     *
     * @throws DomainException When a command class is not valid
     */
    public function registerHandlers(array $commandToHandlerMap): void
    {
        foreach ($commandToHandlerMap as $commandClass => $serviceName) {
            $this->registerHandler($commandClass, $serviceName);
        }
    }

    /**
     * Registers a command handler
     *
     * @param string $commandClass The full command class name
     * @param string $serviceName  The handler service name
     *
     * @return void
     *
     * @throws DomainException When the command class is not valid
     */
    public function registerHandler(string $commandClass, string $serviceName): void
    {
        if (!Validate::implementsInterface($commandClass, CommandInterface::class)) {
            $message = sprintf('Invalid command class: %s', $commandClass);
            throw new DomainException($message);
        }

        $type = Type::create($commandClass)->toString();

        $this->handlers[$type] = $serviceName;
    }

    /**
     * {@inheritdoc}
     */
    public function getHandler(string $commandClass): CommandHandlerInterface
    {
        if (!$this->hasHandler($commandClass)) {
            $message = sprintf('Handler not defined for command: %s', $commandClass);
            throw new LookupException($message);
        }

        $type = Type::create($commandClass)->toString();
        $service = $this->handlers[$type];

        return $this->container->get($service);
    }

    /**
     * {@inheritdoc}
     */
    public function hasHandler(string $commandClass): bool
    {
        $type = Type::create($commandClass)->toString();

        if (!isset($this->handlers[$type])) {
            return false;
        }

        $service = $this->handlers[$type];

        return $this->container->has($service);
    }
}
