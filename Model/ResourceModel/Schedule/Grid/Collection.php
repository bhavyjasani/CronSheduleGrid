<?php

namespace Dolphin\CronGrid\Model\ResourceModel\Schedule\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
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
