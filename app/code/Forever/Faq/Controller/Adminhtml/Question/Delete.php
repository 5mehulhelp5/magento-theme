<?php

declare(strict_types=1);

namespace Forever\Faq\Controller\Adminhtml\Question;

use Exception;
use Forever\Faq\Model\QuestionFactory;
use Forever\Faq\Model\ResourceModel\Question as QuestionResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;

class Delete extends Action
{
    public function __construct(
        Context $context,
        private readonly QuestionFactory $questionFactory,
        private readonly QuestionResource $questionResource
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('Unable to process. please, try again.'));
            return $resultRedirect->setPath('*/*/', ['_current' => true]);
        }

        $model = $this->questionFactory->create();
        $this->questionResource->load($model, $id);

        if (!$model->getId()) {
            $this->messageManager->addErrorMessage(__('Unable to process. please, try again.'));
            return $resultRedirect->setPath('*/*/', ['_current' => true]);
        }

        try {
            $this->questionResource->delete($model);
            $this->messageManager->addSuccessMessage(__('Your data row has been deleted !'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Error while trying to delete row'));
            return $resultRedirect->setPath('*/*/index', ['_current' => true]);
        }

        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
