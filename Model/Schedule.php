<?php

namespace Dolphin\CronGrid\Model;

use Magento\Framework\Exception\LocalizedException;

class Schedule extends \Magento\Cron\Model\Schedule
{
    /**
     * Clears the log data from the cron schedule table
     *
     * This method truncates the table to remove all logged cron execution entries.
     *
     * @return void
     */
    public function clearLog()
    {
        $collection = $this->getResourceCollection();

        $collection->getConnection()->truncateTable($collection->getMainTable());
    }
}
