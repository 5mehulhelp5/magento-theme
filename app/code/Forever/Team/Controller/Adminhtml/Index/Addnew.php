<?php

namespace Forever\Team\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Forever\Team\Model\TeamFactory;
use Magento\Framework\Controller\ResultFactory;

class Addnew extends Action implements HttpGetActionInterface
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
     * @param Context $context
     * @param TeamFactory $entityFactory
     */
    public function __construct(
        Context $context,
        TeamFactory $entityFactory
    ) {
        parent::__construct($context);
        $this->entityFactory = $entityFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->entityFactory->create();

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Forever_Team::first_level_menu');

        $title = "Team Information";
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
