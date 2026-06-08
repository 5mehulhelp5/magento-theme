<?php

namespace Forever\Brand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Forever\Brand\Model\BrandFactory;
use Forever\Brand\Model\ResourceModel\Brand as BrandResource;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Forever_Brand::brand';

    /**
     * @var BrandFactory
     */
    private $entityFactory;

    /**
     * @var BrandResource
     */
    private $brandResource;

    /**
     * @param Context $context
     * @param BrandFactory $entityFactory
     * @param BrandResource $brandResource
     */
    public function __construct(
        Context $context,
        BrandFactory $entityFactory,
        BrandResource $brandResource
    ) {
        parent::__construct($context);
        $this->entityFactory = $entityFactory;
        $this->brandResource = $brandResource;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $model = $this->entityFactory->create();
                $this->brandResource->load($model, $id);
                $this->brandResource->delete($model);

                $this->messageManager->addSuccessMessage(__('Brand has been deleted.'));
                return $resultRedirect->setPath('*/*');

            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find entity to delete.'));
        return $resultRedirect->setPath('*/*');
    }
}
