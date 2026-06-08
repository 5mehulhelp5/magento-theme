<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Forever\StoreLocator\Model\ResourceModel\Store\CollectionFactory;
use Forever\StoreLocator\Model\ResourceModel\Store as StoreResource;

class MassDelete extends Action
{
    /**
     * Authorization level of a basic admin session.
     */
    const ADMIN_RESOURCE = 'Forever_StoreLocator::entity';

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var StoreResource
     */
    private StoreResource $storeResource;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     * @param StoreResource     $storeResource
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        StoreResource $storeResource
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->storeResource     = $storeResource;
        parent::__construct($context);
    }

    /**
     * Mass delete action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection    = $this->filter->getCollection($this->collectionFactory->create());
        $recordDeleted = 0;

        foreach ($collection->getItems() as $record) {
            $this->storeResource->delete($record);
            $recordDeleted++;
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $recordDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
