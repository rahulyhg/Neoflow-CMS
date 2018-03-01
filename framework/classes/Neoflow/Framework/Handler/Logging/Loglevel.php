<?php

namespace Neoflow\Framework\Handler\Logging;

class Loglevel
{
    const EMERGENCY = 0;
    const ALERT = 1;
    const CRITICAL = 2;
    const ERROR = 3;
    const WARNING = 4;
    const NOTICE = 5;
    const INFO = 6;
    const DEBUG = 7;

    /**
     * @var array
     */
    protected static $labels = [
        'EMERGENCY',
        'ALERT',
        'CRITICAL',
        'ERROR',
        'WARNING',
        'NOTICE',
        'INFO',
        'DEBUG',
    ];

    /**
     * Get log level label.
     *
     * @param int $level
     *
     * @return string
     */
    public static function getLabel(int $level): string
    {
        if (self::isValid($level)) {
            return self::$labels[$level];
        }

        return 'UNKNOWN';
    }

    /**
     * Check whether log level is valid.
     *
     * @param int $level
     *
     * @return bool
     */
    public static function isValid(int $level): bool
    {
        return isset(self::$labels[$level]);
    }

    /**
     * Get all log level labels.
     *
     * @return array
     */
    public static function getLabels(): array
    {
        return self::$labels;
    }
}
