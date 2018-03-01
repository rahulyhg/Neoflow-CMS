<?php

namespace Neoflow\Framework\Persistence;

trait QueryCounterTrait
{
    /**
     * Count up executed queries.
     *
     * @return int
     */
    protected function countUpExecutedQueries(): int
    {
        $executedQueries = $this->app()->get('executedQueries');
        $this->app()->set('executedQueries', ++$executedQueries);

        return $executedQueries;
    }

    /**
     * Count up cached queries.
     *
     * @return int
     */
    protected function countUpCachedQueries(): int
    {
        $cachedQueries = $this->app()->get('cachedQueries');
        $this->app()->set('cachedQueries', ++$cachedQueries);

        return $cachedQueries;
    }
}
