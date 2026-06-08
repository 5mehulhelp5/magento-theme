<?php

namespace Forever\Blog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Forever\Blog\Model\BlogFactory;
use Forever\Blog\Model\ResourceModel\Blog as BlogResource;

/**
 * Blog post save controller
 */
class Save extends Action
{
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
     * @param BlogFactory $blogFactory
     * @param BlogResource $blogResource
     */
    public function __construct(
        Context $context,
        BlogFactory $blogFactory,
        BlogResource $blogResource
    ) {
        $this->blogFactory = $blogFactory;
        $this->blogResource = $blogResource;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $rowData = $this->blogFactory->create();
            $id = $this->getRequest()->getParam('blog_id');
            if ($id) {
                $this->blogResource->load($rowData, $id);
            }
            if (!empty($data['tags'])) {
                $tagStr = implode(", ", $data['tags']);
                $rowData->setTags($tagStr);
            } else {
                $rowData->setTags('');
            }
            $rowData->setTitle($data['title']);
            $rowData->setContentHeading($data['content_heading']);
            $rowData->setContent($data['content']);
            if (isset($data['blog_image'][0]['name'])) {
                $rowData->setBlogImage($data['blog_image'][0]['name']);
            } else {
                $rowData->setBlogImage(null);
            }
            $rowData->setStatus($data['status']);
            $rowData->setAuthor($data['author']);

            // Duplicate URL check
            $urlCheckModel = $this->blogFactory->create();
            $this->blogResource->load($urlCheckModel, $data['url_key'], 'url_key');
            if ($id) {
                $rowData->setUrlKey($data['url_key']);
            } else {
                if ($urlCheckModel->getId()) {
                    $this->messageManager->addErrorMessage(__('Duplicate Url not Allowded.'));
                    return $resultRedirect->setPath('*/*/');;
                } else {
                    $rowData->setUrlKey($data['url_key']);
                }
            }
            try {
                $this->blogResource->save($rowData);
                $this->messageManager->addSuccessMessage(__('Row data has been successfully saved.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['blog_id' => $rowData->getBlogId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
            return $resultRedirect->setPath('*/*/edit', ['blog_id' => $this->getRequest()->getParam('blog_id')]);
        }
        return $resultRedirect->setPath('*/*/');;
    }
}
