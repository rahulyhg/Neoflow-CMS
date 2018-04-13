<?php

namespace Neoflow\Framework\Persistence\Querying;

use InvalidArgumentException;

trait WhereTrait
{
    /**
     * Add raw WHERE condition.
     *
     * @param string $condition
     * @param array  $parameters
     *
     * @return AbstractQuery
     */
    public function whereRaw(string $condition, array $parameters = []): AbstractQuery
    {
        return $this->addStatement('WHERE', $condition, $parameters);
    }

    /**
     * Add where condition.
     *
     * @param string $column
     * @param string $operator
     * @param mixed  $parameter
     *
     * @return AbstractQuery
     *
     * @throws InvalidArgumentException
     */
    public function where($column, $operator, $parameter)
    {
        if (in_array($operator, ['<', '>', '=', '!=', 'BETWEEN', 'LIKE', 'IS', 'IS NOT', 'IN'])) {
            if (is_null($parameter)) {
                if ('!=' === $operator) {
                    $operator = 'IS NOT';
                } elseif ('=' === $operator) {
                    $operator = 'IS';
                }
            } elseif (is_array($parameter)) {
                if (count($parameter) > 1) {
                    return $this->addStatement('WHERE', $this->quoteIdentifier($column).' IN ('.implode(',', array_fill(0, count($parameter), '?')).')', $parameter);
                } elseif (1 === count($parameter)) {
                    $parameter = array_values($parameter)[0];
                } else {
                    $parameter = '';
                }
            }

            return $this->addStatement('WHERE', $this->quoteIdentifier($column).' '.$operator.' ?', [$parameter]);
        }
        throw new InvalidArgumentException('WHERE condition operator "'.$operator.'" is invalid');
    }
}
