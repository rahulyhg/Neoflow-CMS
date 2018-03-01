<?php

namespace Neoflow\CMS\Model;

use DateTime;
use Neoflow\CMS\App;
use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\ORM\Repository;

class VisitorModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'visitors';

    /**
     * @var string
     */
    public static $primaryKey = 'visitor_id';

    /**
     * @var array
     */
    public static $properties = ['visitor_id', 'session_key', 'ip_address', 'user_id', 'last_activity', 'user_agent'];

    /**
     * Get repository to fetch user.
     *
     * @return Repository
     */
    public function user()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\UserModel', 'user_id');
    }

    /**
     * Find all current visitors.
     *
     * @return EntityCollection
     */
    public static function findAllCurrent(): EntityCollection
    {
        $sessionLifetime = (int) App::instance()->get('settings')->session_lifetime;

        return self::findAllByTimePeriod(microtime(true) - $sessionLifetime);
    }

    /**
     * Find all visitors by time period.
     *
     * @param int|string|DateTime $startDate Start date
     * @param int|string|DateTime $endDate   An optional end date
     *
     * @return EntityCollection
     */
    public static function findAllByTimePeriod($startDate, $endDate = null): EntityCollection
    {
        if (!is_int($startDate)) {
            if ($startDate instanceof DateTime) {
                $startDate = $startDate->getTimestamp();
            } elseif (is_string($startDate)) {
                $startDate = strtotime($startDate);
            }
        }

        if (!is_int($endDate)) {
            if ($endDate instanceof DateTime) {
                $endDate = $endDate->getTimestamp();
            } elseif (is_string($endDate)) {
                $endDate = strtotime($endDate);
            } else {
                $endDate = strtotime('today 23:59:59');
            }
        }

        return self::repo()
                ->caching(false)
//                        ->groupBy('user_agent')
//                        ->groupBy('ip_address')
                ->where('last_activity', '>', $startDate)
                ->where('last_activity', '<', $endDate)
                ->fetchAll();
    }
}
