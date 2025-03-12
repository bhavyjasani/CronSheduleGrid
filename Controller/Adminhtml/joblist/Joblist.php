<?php

namespace Dolphin\CronGrid\Controller\Adminhtml\joblist;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Joblist
 *
 * Controller action for displaying the cron job list in the admin panel.
 */
class Joblist extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Joblist constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute the controller action to display the cron job list page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Cron Jobs'));

        return $resultPage;
    }
}
