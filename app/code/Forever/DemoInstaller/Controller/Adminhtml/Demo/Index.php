<?php
namespace Forever\DemoInstaller\Controller\Adminhtml\Demo;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Forever_DemoInstaller::demo';

    public function __construct(Context $context, private PageFactory $resultPageFactory)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultPageFactory->create();
        $result->setActiveMenu('Forever_DemoInstaller::demo');
        $result->getConfig()->getTitle()->prepend(__('Forever Demo Importer'));
        return $result;
    }
}
