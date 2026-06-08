<?php

namespace Forever\Brand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Forever\Brand\Model\BrandFactory;
use Magento\Framework\Controller\ResultFactory;
    
class Addnew extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Forever_Brand::brand';
    
    /**
     * @var \Forever\Brand\Model\BrandFactory
     */
    private $entityFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Forever\Brand\Model\EntityFactory $entityFactory
     */
    public function __construct(
        Context $context,
        BrandFactory $entityFactory
    ) {
        parent::__construct($context);
        $this->entityFactory = $entityFactory;
    }

    /**
     * Create new Entity
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        $this->entityFactory->create();
        
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Forever_Brand::first_level_menu');
        
        $title = "Brand Information";
        
        $resultPage->getConfig()->getTitle()->prepend($title);
        
        return $resultPage;
    }
}
