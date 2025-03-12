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
 * MassDelete Controller
 *
 * Handles the deletion of selected cron jobs.
 */
class MassDelete extends \Magento\Backend\App\Action
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
     * MassDelete constructor.
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
     * Execute the controller action to delete selected cron jobs.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $selected = $this->getRequest()->getParams();
                    
            if (isset($selected['selected'])) {
                foreach ($selected['selected'] as $name) {
                    $cronJob = $this->Dolphincron->create();
                    $cronJob->load($name, 'id');
                    $cronJob->delete();
                }
                $this->messageManager->addSuccess(__('Selected cron jobs have been deleted.'));
            } else {
                $collection = $this->filter->getCollection($this->dolphinCronFactory->create());
                foreach ($collection as $name) {
                    $name->delete();
                }

                $this->messageManager->addSuccess(__('Selected cron jobs have been deleted.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/joblist');
    }
}
