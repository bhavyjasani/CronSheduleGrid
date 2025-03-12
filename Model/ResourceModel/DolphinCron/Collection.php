<?php

namespace Dolphin\CronGrid\Model\ResourceModel\DolphinCron;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Dolphin\CronGrid\Model\DolphinCron as Cronmodel;
use Dolphin\CronGrid\Model\ResourceModel\DolphinCron as Cronresourcemodel;

/**
 * Class Collection
 *
 * Collection class for the Store model
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize the collection
     */
    protected function _construct()
    {
        $this->_init(Cronmodel::class, cronresourcemodel::class);
    }
}
