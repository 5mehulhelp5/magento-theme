<?php

namespace Forever\Brand\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Forever\Brand\Model\ResourceModel\Brand as BrandResource;
use Forever\Brand\Model\ResourceModel\Brand\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Forever_Brand::brand';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var BrandResource
     */
    protected $brandResource;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param BrandResource $brandResource
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        BrandResource $brandResource
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->brandResource     = $brandResource;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection    = $this->filter->getCollection($this->collectionFactory->create());
        $recordDeleted = 0;

        foreach ($collection->getItems() as $record) {
            $this->brandResource->delete($record);
            $recordDeleted++;
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $recordDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
