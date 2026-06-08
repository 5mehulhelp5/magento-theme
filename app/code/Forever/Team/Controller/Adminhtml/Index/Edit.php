<?php

namespace Forever\Team\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Forever\Team\Model\TeamFactory;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Forever_Team::team';

    /**
     * @var TeamFactory
     */
    private $entityFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param Context $context
     * @param TeamFactory $entityFactory
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        TeamFactory $entityFactory,
        RequestInterface $request
    ) {
        parent::__construct($context);
        $this->entityFactory = $entityFactory;
        $this->request = $request;
    }

    public function execute()
    {
        $rowId = (int) $this->request->getParam('id');
        $rowData = $this->entityFactory->create();

        if ($rowId) {
            $rowData = $rowData->load($rowId);

            if (!$rowData->getId()) {
                $this->messageManager->addErrorMessage(__('row data no longer exist.'));
                $this->_redirect('*/*/index');
                return;
            }
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Forever_Team::first_level_menu');
        $title = "Team Information";
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
