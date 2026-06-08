<?php

namespace Forever\Blog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Forever\Blog\Model\ResourceModel\Blog\CollectionFactory;
use Forever\Blog\Model\ResourceModel\Blog as BlogResource;

class MassDelete extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var BlogResource
     */
    protected $blogResource;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param BlogResource $blogResource
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Filter $filter,
        BlogResource $blogResource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->blogResource = $blogResource;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $recordDeleted = 0;

        foreach ($collection->getItems() as $record) {
            $this->blogResource->delete($record);
            $recordDeleted++;
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $recordDeleted));

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('blog/index/index');
    }

    /**
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Forever_Blog::home');
    }
}
