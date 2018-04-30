<?php
namespace Neoflow\Module\Search;

interface ModelInterface
{

    /**
     * Search for results
     * @param string $query
     *
     * @return Results
     */
    public static function search(string $query): Results;
}
