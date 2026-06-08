<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Faq\Controller\Adminhtml\Question;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Forever\Faq\Model\QuestionFactory;

/**
 * @Class Save
 * package Forever\Faq\Controller\Adminhtml\Question
 */
class Save extends Action
{
    private $collectionFactory;

    public function __construct(Action\Context $context, QuestionFactory $collectionFactory)
    {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $rowData = $this->collectionFactory->create();
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $rowData->load($id);
            }
            $rowData->setStatus($data['status']);
            $rowData->setTitle($data['title']);
            $rowData->setAnswer($data['answer']);
            try {
                $rowData->save();
                $this->messageManager->addSuccessMessage(__('Row data has been successfully saved.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $rowData->getBlogId(),
                    '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Data.'));
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
