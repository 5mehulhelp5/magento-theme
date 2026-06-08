<?php

namespace Forever\Blog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Forever\Blog\Model\BlogFactory;
use Forever\Blog\Model\ResourceModel\Blog as BlogResource;

class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var BlogFactory
     */
    protected $blogFactory;

    /**
     * @var BlogResource
     */
    protected $blogResource;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BlogFactory $blogFactory
     * @param BlogResource $blogResource
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BlogFactory $blogFactory,
        BlogResource $blogResource
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->blogFactory = $blogFactory;
        $this->blogResource = $blogResource;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('blog_id');
        $model = $this->blogFactory->create();
        $this->blogResource->load($model, $id);

        if (!$model->getId()) {
            $this->messageManager->addErrorMessage(__('Unable to process. please, try again.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/', ['_current' => true]);
        }
        try {
            $this->blogResource->delete($model);
            $this->messageManager->addSuccessMessage(__('Your data row has been deleted !'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error while trying to delete row'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index', ['_current' => true]);
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
