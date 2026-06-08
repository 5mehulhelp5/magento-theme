<?php

namespace Forever\Megamenu\Controller\Adminhtml\Menu;

use Magento\Framework\Controller\ResultFactory;

class Addrow extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry $coreRegistry
     */
    private $coreRegistry;

    /**
     * @var \Forever\Megamenu\Model\MegamenuFactory $gridFactory
     */
    private $gridFactory;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Forever\Megamenu\Model\MegamenuFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Forever\Megamenu\Model\MegamenuFactory $gridFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->gridFactory = $gridFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        //megamwnu_id to id
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->gridFactory->create();
        $rowData = $rowData->load($rowId);
        $rowTitle = $rowData->getTitle();
            
        $this->coreRegistry->register('row_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Menu ') . $rowTitle : __('Add New Menu');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    /**
     * is allowed
     *
     * @return row
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Forever_Megamenu::add_row');
    }
}
