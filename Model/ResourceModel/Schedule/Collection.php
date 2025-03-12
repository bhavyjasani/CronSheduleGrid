<?php

namespace Dolphin\CronGrid\Model\ResourceModel\Schedule;

class Collection extends \Magento\Cron\Model\ResourceModel\Schedule\Collection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'schedule_id';

    /**
     * Initialize select statement.
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addExpressionFieldToSelect('total_time', 'TIMEDIFF(finished_at, executed_at)', []);

        return $this;
    }
}
