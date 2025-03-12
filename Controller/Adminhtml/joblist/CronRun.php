<?php

namespace Dolphin\CronGrid\Controller\Adminhtml\joblist;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Action;
use Dolphin\CronGrid\Model\SpecificJobRun;

/**
 * CronRun Controller
 *
 * Handles the execution of specific cron jobs from the admin panel.
 */
class CronRun extends Action
{
    /**
     * @var SpecificJobRun
     */
    protected $command;

    /**
     * CronRun constructor.
     *
     * @param Context $context
     * @param SpecificJobRun $command
     */
    public function __construct(
        Context $context,
        SpecificJobRun $command
    ) {
        $this->command = $command;
        parent::__construct($context);
    }

    /**
     * Executes the cron job if a valid cron job code is provided.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getParams();
            
            if (!empty($data['name'])) {
                $this->command->runJob($data['name']);
                $this->messageManager->addSuccessMessage(__('Command has been run.'));
            } else {
                $this->messageManager->addErrorMessage(__("Invalid Cron code"));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        return $this->_redirect('*/*/joblist');
    }
}
