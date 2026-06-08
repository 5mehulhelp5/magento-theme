<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Forever\StoreLocator\Model\StoreFactory;
use Forever\StoreLocator\Model\ResourceModel\Store as StoreResource;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session.
     */
    const ADMIN_RESOURCE = 'Forever_StoreLocator::store';

    /**
     * @var Registry
     */
    private Registry $coreRegistry;

    /**
     * @var StoreFactory
     */
    private StoreFactory $storeFactory;

    /**
     * @var StoreResource
     */
    private StoreResource $storeResource;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param Registry                            $coreRegistry
     * @param StoreFactory                        $storeFactory
     * @param StoreResource                       $storeResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Registry $coreRegistry,
        StoreFactory $storeFactory,
        StoreResource $storeResource
    ) {
        parent::__construct($context);
        $this->coreRegistry  = $coreRegistry;
        $this->storeFactory  = $storeFactory;
        $this->storeResource = $storeResource;
    }

    /**
     * Edit action.
     *
     * @return \Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        $rowId   = (int) $this->getRequest()->getParam('store_id');
        $rowData = $this->storeFactory->create();

        if ($rowId) {
            $this->storeResource->load($rowData, $rowId);
            if (!$rowData->getStoreId()) {
                $this->messageManager->addErrorMessage(__('row data no longer exist.'));
                $this->_redirect('*/*/index');
                return;
            }
        }

        $this->coreRegistry->register('row_data', $rowData);

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Forever_StoreLocator::index');
        $resultPage->getConfig()->getTitle()->prepend(__('Store Information'));

        return $resultPage;
    }
}
