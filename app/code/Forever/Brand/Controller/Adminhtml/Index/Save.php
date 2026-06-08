<?php

namespace Forever\Brand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Forever\Brand\Model\BrandFactory;
use Forever\Brand\Model\ResourceModel\Brand as BrandResource;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Forever_Brand::brand';

    /**
     * @var BrandFactory
     */
    protected $entityFactory;

    /**
     * @var BrandResource
     */
    protected $brandResource;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param BrandFactory $entityFactory
     * @param BrandResource $brandResource
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        BrandFactory $entityFactory,
        BrandResource $brandResource,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->entityFactory     = $entityFactory;
        $this->brandResource     = $brandResource;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Save action
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data           = $this->getRequest()->getPostValue();

        try {
            $entityModel = $this->entityFactory->create();

            if (!empty($data['id'])) {
                $this->brandResource->load($entityModel, $data['id']);
            }

            $entityModel->setData('orders', $data['orders']);
            $entityModel->setData('status', $data['status']);

            if (isset($data['image'][0]['name'])) {
                $entityModel->setData('image', $data['image'][0]['name']);
            } else {
                $entityModel->setData('image', null);
            }

            $this->brandResource->save($entityModel);

            $this->messageManager->addSuccessMessage(__('The Brand has been saved.'));

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['id' => $entityModel->getId(), '_current' => true, '_use_rewrite' => true]
                );
            }

            return $resultRedirect->setPath('*/*');

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the Brand.'));
        }

        return $resultRedirect->setPath('*/*');
    }
}
