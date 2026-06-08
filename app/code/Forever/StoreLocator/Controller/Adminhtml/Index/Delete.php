<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Controller\Adminhtml\Index;

use Forever\StoreLocator\Model\StoreFactory;
use Forever\StoreLocator\Model\ResourceModel\Store as StoreResource;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session.
     */
    const ADMIN_RESOURCE = 'Forever_StoreLocator::entity';

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
     * @param StoreFactory                        $storeFactory
     * @param StoreResource                       $storeResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        StoreFactory $storeFactory,
        StoreResource $storeResource
    ) {
        parent::__construct($context);
        $this->storeFactory  = $storeFactory;
        $this->storeResource = $storeResource;
    }

    /**
     * Delete action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam('store_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $model = $this->storeFactory->create();
                $this->storeResource->load($model, $id);
                $this->storeResource->delete($model);
                $this->messageManager->addSuccessMessage(__('Store has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['store_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__("We can't find store to delete."));
        return $resultRedirect->setPath('*/*/');
    }
}
