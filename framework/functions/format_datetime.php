<?php

use Neoflow\Framework\App;

/**
 * Format DateTime object.
 *
 * @param DateTime $dateTime
 * @param bool     $formatWithTime
 *
 * @return string
 */
function format_datetime(DateTime $dateTime, bool $formatWithTime = true)
{
    return App::instance()->get('translator')->formatDateTime($dateTime, $formatWithTime);
}
