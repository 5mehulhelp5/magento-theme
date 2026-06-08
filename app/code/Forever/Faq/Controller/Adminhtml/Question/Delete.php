<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Faq\Controller\Adminhtml\Question;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var $resultPageFactory
     */
    protected $resultPageFactory;
    /**
     * @var $questionFactory
     */
    protected $questionFactory;
    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Forever\Blog\Model\BlogFactory            $questionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Forever\Faq\Model\QuestionFactory $questionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->questionFactory = $questionFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $contact = $this->questionFactory->create()->load($id);
        if (!$contact) {
            $this->messageManager->addErrorMessage(__('Unable to process. please, try again.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/', ['_current' => true]);
        }
        try {
            $contact->delete();
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
