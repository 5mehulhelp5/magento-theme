<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var $resultPageFactory
     */
    protected $resultPageFactory;
    /**
     * @var $_publicActions
     */
    protected $publicActions = ['index'];
    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
     
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Manage Blog ')));
        $this->_setActiveMenu('Forever_Blog::index_index');
 
        $resultPage->addBreadcrumb(__('blog'), __('Index'));
        $resultPage->addBreadcrumb(__('blog'), __('Manage Blog '));
 
        return $resultPage;
    }
    /**
     * @return $_isAllowed
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Forever_Blog::index_index');
    }
}
