<?php

declare(strict_types=1);

namespace Forever\Faq\Controller\Adminhtml\Question;

use Exception;
use Forever\Faq\Model\QuestionFactory;
use Forever\Faq\Model\ResourceModel\Question as QuestionResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;

class Save extends Action
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
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $model = $this->questionFactory->create();
            $id = $this->getRequest()->getParam('id');

            if ($id) {
                $this->questionResource->load($model, $id);
            }

            $model->setStatus($data['status']);
            $model->setTitle($data['title']);
            $model->setAnswer($data['answer']);

            try {
                $this->questionResource->save($model);
                $this->messageManager->addSuccessMessage(__('Row data has been successfully saved.'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
