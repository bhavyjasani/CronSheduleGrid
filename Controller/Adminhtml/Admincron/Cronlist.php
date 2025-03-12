<?php

namespace Dolphin\CronGrid\Controller\Adminhtml\Admincron;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Cronlist Controller
 *
 * This controller handles the display of the Cron Task List in the Admin panel.
 * It loads the page and sets the title for the Cron Task List page.
 */
class Cronlist extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Cronlist constructor.
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
     * Execute fuction to genrate the page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Cron Task List'));
        return $resultPage;
    }
}
