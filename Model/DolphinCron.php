<?php

namespace Dolphin\CronGrid\Model;

use Magento\Framework\Model\AbstractModel;
use Dolphin\CronGrid\Model\ResourceModel\DolphinCron as CronData;

/**
 * Class ImportHistory for collectionfacory
 */
class DolphinCron extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CronData::class);
    }
}
