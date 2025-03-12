<?php

namespace Dolphin\CronGrid\Controller\Adminhtml\joblist;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory;
use Dolphin\CronGrid\Model\ResourceModel\DolphinCron\CollectionFactory as DolphinCronFactory;
use Dolphin\CronGrid\Model\DolphinCronFactory as Dolphincron;

/**
 * Class Enable
 *
 * Controller action for enabling cron jobs from the admin panel.
 */
class Enable extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DolphinCronFactory
     */
    protected $dolphinCronFactory;

    /**
     * @var Dolphincron
     */
    protected $Dolphincron;

    /**
     * Enable constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param DolphinCronFactory $dolphinCronFactory
     * @param Dolphincron $Dolphincron
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DolphinCronFactory $dolphinCronFactory,
        Dolphincron $Dolphincron
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->dolphinCronFactory = $dolphinCronFactory;
        $this->Dolphincron = $Dolphincron;
        parent::__construct($context);
    }

    /**
     * Enable selected cron jobs or all cron jobs.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $selected = $this->getRequest()->getParams();
            if (isset($selected['selected'])) {
                foreach ($selected['selected'] as $name) {
                    $cronJob = $this->Dolphincron->create();
                    $cronJob->load($name, 'id');
                    $cronJob->setVisibility(1);
                    $cronJob->save();
                }
                $this->messageManager->addSuccess(__('Selected cron jobs are enabled.'));
            } else {
                $collection = $this->filter->getCollection($this->dolphinCronFactory->create());
                foreach ($collection as $name) {
                    $name->setVisibility(1);
                    $name->save();
                }

                $this->messageManager->addSuccess(__('All cron jobs are enabled.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/joblist');
    }
}
