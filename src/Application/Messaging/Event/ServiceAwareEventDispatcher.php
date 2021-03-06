<?php declare(strict_types=1);

namespace Novuso\Common\Application\Messaging\Event;

use Novuso\Common\Domain\Messaging\Event\EventMessage;
use Novuso\Common\Domain\Messaging\Event\EventSubscriber;
use Novuso\System\Exception\AssertionException;
use Novuso\System\Exception\TypeException;
use Novuso\System\Utility\Assert;
use Novuso\System\Utility\ClassName;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Class ServiceAwareEventDispatcher
 */
final class ServiceAwareEventDispatcher extends SimpleEventDispatcher
{
    /**
     * Service container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Services
     *
     * @var array
     */
    protected $services = [];

    /**
     * Service IDs
     *
     * @var array
     */
    protected $serviceIds = [];

    /**
     * Constructs ServiceAwareEventDispatcher
     *
     * @param ContainerInterface $container The service container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Registers a subscriber service to handle events
     *
     * The subscriber class must implement:
     * RateGenius\Common\Domain\Messaging\Event\EventSubscriber
     *
     * @param string $className The subscriber class name
     * @param string $serviceId The subscriber service ID
     *
     * @return void
     *
     * @throws AssertionException When a subscriber class is not valid
     * @throws TypeException When the event type is not valid
     */
    public function registerService(string $className, string $serviceId): void
    {
        Assert::implementsInterface($className, EventSubscriber::class);
        /** @var EventSubscriber $className The subscriber class name */
        foreach ($className::eventRegistration() as $eventType => $params) {
            $eventType = ClassName::underscore($eventType);
            if (is_string($params)) {
                $this->addHandlerService($eventType, $serviceId, $params);
            } elseif (is_string($params[0])) {
                $priority = isset($params[1]) ? (int) $params[1] : 0;
                $this->addHandlerService($eventType, $serviceId, $params[0], $priority);
            } else {
                foreach ($params as $handler) {
                    $priority = isset($handler[1]) ? (int) $handler[1] : 0;
                    $this->addHandlerService($eventType, $serviceId, $handler[0], $priority);
                }
            }
        }
    }

    /**
     * Adds a handler service for a specific event
     *
     * @param string $eventType The event type
     * @param string $serviceId The handler service ID
     * @param string $method    The name of the method to invoke
     * @param int    $priority  Higher priority handlers are called first
     *
     * @return void
     */
    public function addHandlerService(string $eventType, string $serviceId, string $method, int $priority = 0): void
    {
        if (!isset($this->serviceIds[$eventType])) {
            $this->serviceIds[$eventType] = [];
        }

        $this->serviceIds[$eventType][] = [$serviceId, $method, $priority];
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(EventMessage $message): void
    {
        $this->lazyLoad(ClassName::underscore($message->payload()));

        parent::dispatch($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlers(?string $eventType = null): array
    {
        if ($eventType === null) {
            foreach (array_keys($this->serviceIds) as $type) {
                $this->lazyLoad($type);
            }
        } else {
            $this->lazyLoad($eventType);
        }

        return parent::getHandlers($eventType);
    }

    /**
     * {@inheritdoc}
     */
    public function hasHandlers(?string $eventType = null): bool
    {
        if ($eventType === null) {
            return (bool) count($this->serviceIds) || (bool) count($this->handlers);
        }

        if (isset($this->serviceIds[$eventType])) {
            return true;
        }

        return parent::hasHandlers($eventType);
    }

    /**
     * {@inheritdoc}
     */
    public function removeHandler(string $eventType, callable $handler): void
    {
        $this->lazyLoad($eventType);

        if (isset($this->serviceIds[$eventType])) {
            foreach ($this->serviceIds[$eventType] as $i => $args) {
                list($serviceId, $method) = $args;
                $key = sprintf('%s.%s', $serviceId, $method);
                if (isset($this->services[$eventType][$key])
                    && $handler === [$this->services[$eventType][$key], $method]) {
                    unset($this->services[$eventType][$key]);
                    if (empty($this->services[$eventType])) {
                        unset($this->services[$eventType]);
                    }
                    unset($this->serviceIds[$eventType][$i]);
                    if (empty($this->serviceIds[$eventType])) {
                        unset($this->serviceIds[$eventType]);
                    }
                }
            }
        }

        parent::removeHandler($eventType, $handler);
    }

    /**
     * Lazy loads event handlers from the service container
     *
     * @param string $eventType The event type
     *
     * @return void
     *
     * @throws ContainerExceptionInterface When an error occurs
     */
    protected function lazyLoad(string $eventType): void
    {
        if (isset($this->serviceIds[$eventType])) {
            foreach ($this->serviceIds[$eventType] as $args) {
                list($serviceId, $method, $priority) = $args;
                $service = $this->container->get($serviceId);
                $key = sprintf('%s.%s', $serviceId, $method);
                if (!isset($this->services[$eventType][$key])) {
                    $this->addHandler($eventType, [$service, $method], $priority);
                } elseif ($service !== $this->services[$eventType][$key]) {
                    parent::removeHandler($eventType, [$this->services[$eventType][$key], $method]);
                    $this->addHandler($eventType, [$service, $method], $priority);
                }
                $this->services[$eventType][$key] = $service;
            }
        }
    }
}
