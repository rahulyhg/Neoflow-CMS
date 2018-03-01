<?php

namespace Neoflow\Framework\Persistence;

use Neoflow\Framework\AppTrait;
use Neoflow\Framework\Persistence\Querying\DeleteQuery;
use Neoflow\Framework\Persistence\Querying\InsertQuery;
use Neoflow\Framework\Persistence\Querying\SelectQuery;
use Neoflow\Framework\Persistence\Querying\UpdateQuery;

class QueryBuilder
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Create SELECT query.
     *
     * @param string $table   Name of table
     * @param array  $columns Name of columns to select
     *
     * @return SelectQuery
     */
    public function selectFrom(string $table, array $columns = []): SelectQuery
    {
        $query = new SelectQuery($table);
        foreach ($columns as $column) {
            $query->select($column);
        }

        return $query;
    }

    /**
     * Create DELETE query.
     *
     * @param string $table Name of table
     *
     * @return DeleteQuery
     */
    public function deleteFrom(string $table): DeleteQuery
    {
        return new DeleteQuery($table);
    }

    /**
     * Create INSERT query.
     *
     * @param string $table  Name of table
     * @param array  $values Values as associative array (column => value)
     *
     * @return InsertQuery
     */
    public function insertInto(string $table, array $values = []): InsertQuery
    {
        $query = new InsertQuery($table);

        return $query->values($values);
    }

    /**
     * Create UPDATE query.
     *
     * @param string $table Name of table
     * @param array  $set   Values as associative array (column => value)
     *
     * @return UpdateQuery
     */
    public function update(string $table, array $set = []): UpdateQuery
    {
        $query = new UpdateQuery($table);

        return $query->set($set);
    }
}
