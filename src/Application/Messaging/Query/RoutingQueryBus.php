<?php declare(strict_types=1);

namespace Novuso\Common\Application\Messaging\Query;

use Novuso\Common\Application\Messaging\Query\Routing\QueryRouter;
use Novuso\Common\Domain\Messaging\Query\Query;
use Novuso\Common\Domain\Messaging\Query\QueryBus;
use Novuso\Common\Domain\Messaging\Query\QueryMessage;

/**
 * Class RoutingQueryBus
 */
final class RoutingQueryBus implements QueryBus
{
    /**
     * Query router
     *
     * @var QueryRouter
     */
    protected $router;

    /**
     * Constructs RoutingQueryBus
     *
     * @param QueryRouter $router The query router
     */
    public function __construct(QueryRouter $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Query $query)
    {
        return $this->dispatch(QueryMessage::create($query));
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(QueryMessage $message)
    {
        /** @var Query $query */
        $query = $message->payload();

        return $this->router->match($query)->handle($message);
    }
}
