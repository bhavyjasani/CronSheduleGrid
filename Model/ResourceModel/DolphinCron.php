<?php

namespace Dolphin\CronGrid\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class DolphinCron
 *
 * Resource model for the Dolphin Cron entity.
 */
class DolphinCron extends AbstractDb
{
    /**
     * @var string
     */
    public const MAINTABLE = 'Dolphin_Cron';

    /**
     * @var string
     */
    public const ID_FIELD_NAME = 'id';
    
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(self::MAINTABLE, self::ID_FIELD_NAME);
    }
}
