<?php
namespace Neoflow\Module\Search;

interface ModelSearchInterface
{

    /**
     * Search for results
     * @param string $query Seach query string
     *
     * @return Results
     */
    public static function search(string $query): Results;
}
