<?php declare(strict_types=1);

namespace Novuso\Common\Domain\Messaging\Query;

use Throwable;

/**
 * Interface QueryHandler
 */
interface QueryHandler
{
    /**
     * Retrieves query registration
     *
     * Returns the fully qualified class name for the query that this service
     * is meant to handle.
     *
     * @return string
     */
    public static function queryRegistration(): string;

    /**
     * Handles a query
     *
     * @param QueryMessage $message The query message
     *
     * @return mixed
     *
     * @throws Throwable When an error occurs
     */
    public function handle(QueryMessage $message);
}
