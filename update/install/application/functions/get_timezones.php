<?php

/**
 * Get timezones.
 *
 * @return array
 */
function get_timezones(): array
{
    $regions = [
        'Africa' => DateTimeZone::AFRICA,
        'America' => DateTimeZone::AMERICA,
        'Antarctica' => DateTimeZone::ANTARCTICA,
        'Asia' => DateTimeZone::ASIA,
        'Atlantic' => DateTimeZone::ATLANTIC,
        'Arctic' => DateTimeZone::ARCTIC,
        'Australia' => DateTimeZone::AUSTRALIA,
        'Europe' => DateTimeZone::EUROPE,
        'Indian' => DateTimeZone::INDIAN,
        'Pacific' => DateTimeZone::PACIFIC,
    ];
    $result = [];
    $defaultTimezone = date_default_timezone_get();
    foreach ($regions as $region => $mask) {
        $timezones = DateTimeZone::listIdentifiers($mask);
        foreach ($timezones as $timezone) {
            date_default_timezone_set($timezone);

            $title = str_replace(['/', '_', 'St '], [', ', ' ', 'St. '], mb_substr($timezone, mb_strpos($timezone, '/') + 1));
            $result[$region][$timezone] = '(GMT '.date('P').') '.$title;
        }
        array_multisort($result[$region]);
    }
    array_multisort($result);
    date_default_timezone_set($defaultTimezone);

    return $result;
}
