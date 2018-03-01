
<?php

use Neoflow\Framework\App;

/**
 * Format timestamp.
 *
 * @param int  $timestamp
 * @param bool $formatWithTime
 *
 * @return string
 */
function format_timestamp(int $timestamp, bool $formatWithTime = true)
{
    return App::instance()->get('translator')->formatTimestamp($timestamp, $formatWithTime);
}
