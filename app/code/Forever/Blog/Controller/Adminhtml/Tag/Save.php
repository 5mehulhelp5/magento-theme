<?php

namespace Forever\Blog\Controller\Adminhtml\Tag;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Forever\Blog\Model\TagFactory;
use Forever\Blog\Model\ResourceModel\Tag as TagResource;

class Save extends Action
{
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
     * @param TagFactory $tagFactory
     * @param TagResource $tagResource
     */
    public function __construct(
        Context $context,
        TagFactory $tagFactory,
        TagResource $tagResource
    ) {
        $this->tagFactory = $tagFactory;
        $this->tagResource = $tagResource;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $tagData = $this->tagFactory->create();
            $id = $this->getRequest()->getParam('tag_id');
            if ($id) {
                $this->tagResource->load($tagData, $id);
            }
            $tagData->setStatus($data['status']);
            $tagData->setTitle($data['title']);
            try {
                $this->tagResource->save($tagData);
                $this->messageManager->addSuccessMessage(__('Row data has been successfully saved.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/tag/edit', ['tag_id' => $tagData->getTagId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/tag/index');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
            return $resultRedirect->setPath('*/tag/edit', ['tag_id' => $this->getRequest()->getParam('tag_id')]);
        }
        return $resultRedirect->setPath('*/tag/index');
    }
}
