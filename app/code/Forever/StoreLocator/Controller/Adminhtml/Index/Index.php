<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Controller\Adminhtml\Index;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session.
     */
    const ADMIN_RESOURCE = 'Forever_StoreLocator::store';

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param PageFactory                         $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Load the page defined in view/adminhtml/layout/storelocator_index_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Forever_StoreLocator::index');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Store'));
        return $resultPage;
    }
}
