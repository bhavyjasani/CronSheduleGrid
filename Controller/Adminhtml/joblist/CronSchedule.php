<?php

namespace Dolphin\CronGrid\Controller\Adminhtml\joblist;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Cron\Model\ScheduleFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Dolphin\CronGrid\Model\DolphinCronFactory;

/**
 * Class CronSchedule
 *
 * Controller action for managing cron job schedules in the admin panel.
 */
class CronSchedule extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var ScheduleCollectionFactory
     */
    protected $scheduleCollectionFactory;

    /**
     * @var ScheduleFactory
     */
    protected $scheduleFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var DolphinCronFactory
     */
    protected $dolphinCronFactory;

    /**
     * CronSchedule constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param WriterInterface $configWriter
     * @param TypeListInterface $cacheTypeList
     * @param ScheduleCollectionFactory $scheduleCollectionFactory
     * @param ScheduleFactory $scheduleFactory
     * @param DateTime $dateTime
     * @param DolphinCronFactory $dolphinCronFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        ScheduleCollectionFactory $scheduleCollectionFactory,
        ScheduleFactory $scheduleFactory,
        DateTime $dateTime,
        DolphinCronFactory $dolphinCronFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->scheduleFactory = $scheduleFactory;
        $this->dateTime = $dateTime;
        $this->dolphinCronFactory = $dolphinCronFactory;
    }

    /**
     * Execute the cron schedule update process.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);

        foreach ($postItems as $cronCode => $data) {
            try {
                if (isset($data['modify_schedule']) && !$this->validateCronExpression($data['modify_schedule'])
                                && !empty($data['modify_schedule'])) {
                    throw new LocalizedException(__('Invalid cron expression format: %1', $data['modify_schedule']));
                }

                $cronJob = $this->dolphinCronFactory->create();
                $cronJob->load($cronCode, 'id');

                if ($cronJob->getId()) {
                    if (isset($data['visibility'])) {
                        $cronJob->setVisibility((int) $data['visibility']);
                    }

                    if (isset($data['modify_schedule'])) {
                        $cronJob->setModifySchedule($data['modify_schedule']);
                    }

                    $cronJob->save();

                    $messages[] = __('Cron job "%1" has been updated successfully.', $cronCode);
                } else {
                    $messages[] = __('Cron job "%1" not found.', $cronCode);
                    $error = true;
                }
            } catch (LocalizedException $e) {
                $messages[] = $e->getMessage();
                $error = true;
            } catch (\Exception $e) {
                $messages[] = __('Could not save the cron job: %1', $e->getMessage());
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Validate the cron expression format
     *
     * @param string $expression
     * @return bool
     */
    private function validateCronExpression($expression)
    {
        $parts = explode(' ', trim($expression));
        if (count($parts) !== 5) {
            return false;
        }
        $pattern = '/^(\*|([0-9]+|\*\/[0-9]+|\d+(-\d+)?(,\d+(-\d+)?)*))$/';

        foreach ($parts as $part) {
            if (!preg_match($pattern, $part)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the user has permission to access this controller.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dolphin_CronGrid::cron_manage');
    }
}
