<?php

namespace Neoflow\Module\LogViewer\Service;

use DateTime;
use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Handler\Config;

class LogfileService extends AbstractService
{
    /**
     * @var Config
     */
    protected $logConfig;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->logConfig = $this->config()->get('logger');
    }

    /**
     * Get date of logfile.
     *
     * @param string $logfilePath
     *
     * @return string
     */
    public function getLogfileDate(string $logfilePath): string
    {
        return str_replace($this->logConfig->get('prefix'), '', basename($logfilePath, '.'.$this->logConfig->get('extension')));
    }

    /**
     * Get date of logfile as timestamp.
     *
     * @param string $logfilePath
     *
     * @return int
     */
    public function getLogfileDateAsTimestamp(string $logfilePath): int
    {
        $logfileDate = $this->getLogfileDate($logfilePath);

        return strtotime($logfileDate);
    }

    /**
     * Get date of logfile as datetime.
     *
     * @param string $logfilePath
     *
     * @return DateTime
     */
    public function getLogfileDateAsDatetime(string $logfilePath): DateTime
    {
        $logfileTimestamp = $this->getLogfileDateAsTimestamp($logfilePath);
        $logfileDateTime = new DateTime();

        return $logfileDateTime->setTimestamp($logfileTimestamp);
    }
}
