<?php

namespace Dolphin\CronGrid\Controller\Adminhtml\joblist;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Dolphin\CronGrid\Model\Command;

/**
 * Class Run
 *
 * Controller action to run a command from the admin panel.
 */
class Run extends Action
{
    /**
     * @var Command
     */
    private $command;

    /**
     * Run constructor.
     *
     * @param Context $context
     * @param Command $command
     */
    public function __construct(
        Context $context,
        Command $command
    ) {
        $this->command = $command;

        parent::__construct($context);
    }

    /**
     * Execute the command and provide feedback to the admin.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $this->command->run();
            $this->messageManager->addSuccessMessage(__('Command has been run.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $this->_redirect('*/*/joblist');
    }
}
