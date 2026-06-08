<?php

namespace Forever\Blog\Controller\Adminhtml\Tag;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Forever\Blog\Model\TagFactory;
use Forever\Blog\Model\ResourceModel\Tag as TagResource;

class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var TagFactory
     */
    protected $tagFactory;

    /**
     * @var TagResource
     */
    protected $tagResource;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TagFactory $tagFactory
     * @param TagResource $tagResource
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TagFactory $tagFactory,
        TagResource $tagResource
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->tagFactory = $tagFactory;
        $this->tagResource = $tagResource;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('tag_id');
        $model = $this->tagFactory->create();
        $this->tagResource->load($model, $id);

        if (!$model->getId()) {
            $this->messageManager->addErrorMessage(__('Unable to process. please, try again.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/tag/index', ['_current' => true]);
        }
        try {
            $this->tagResource->delete($model);
            $this->messageManager->addSuccessMessage(__('Your data row has been deleted !'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error while trying to delete row'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/tag/index', ['_current' => true]);
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/tag/index', ['_current' => true]);
    }
}
