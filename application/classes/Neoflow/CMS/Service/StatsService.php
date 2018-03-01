<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Model\VisitorModel;
use Neoflow\CMS\Core\AbstractService;

class StatsService extends AbstractService
{
    /**
     * Get number of current visitors.
     *
     * @return int
     */
    public function getNumberOfCurrentVisitors(): int
    {
        return VisitorModel::findAllCurrent()->count();
    }

    /**
     * Reset visitor stats.
     *
     * @return bool
     */
    public function reset(): bool
    {
        return VisitorModel::findAll()->delete();
    }

    /**
     * Get number of visitors by date.
     *
     * @param string|DateTime $startDate Start date
     * @param string|DateTime $endDate   An optional end date
     *
     * @return int
     */
    public function getNumberOfVisitorsByTimePeriod($startDate = 'today', $endDate = null): int
    {
        return VisitorModel::findAllByTimePeriod($startDate, $endDate)->count();
    }

    /**
     * Get number of visitors from today.
     *
     * @return int
     */
    public function getNumberOfVisitorsToday(): int
    {
        return $this->getNumberOfVisitorsByTimePeriod('today');
    }

    /**
     * Get number of visitiros from this month.
     *
     * @return int
     */
    public function getNumberOfVisitorsThisMonth(): int
    {
        return $this->getNumberOfVisitorsByTimePeriod('midnight first day of this month');
    }

    /**
     * Get total number of visitors.
     *
     * @return int
     */
    public function getTotalNumberOfVisitors(): int
    {
        return $this->getNumberOfVisitorsByTimePeriod('first day of 1970');
    }
}
