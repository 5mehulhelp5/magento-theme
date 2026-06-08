<?php

namespace Forever\Brand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Forever\Brand\Model\BrandFactory;
use Forever\Brand\Model\ResourceModel\Brand as BrandResource;

class Edit extends Action
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
        $rowId   = (int) $this->getRequest()->getParam('id');
        $rowData = $this->entityFactory->create();

        if ($rowId) {
            $this->brandResource->load($rowData, $rowId);

            if (!$rowData->getId()) {
                $this->messageManager->addErrorMessage(__('Row data no longer exist.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/index');
            }
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Forever_Brand::first_level_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Brand Information'));

        return $resultPage;
    }
}
