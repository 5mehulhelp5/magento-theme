<?php
namespace Forever\DemoInstaller\Controller\Adminhtml\Demo;

use Forever\DemoInstaller\Model\DemoManager;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Import extends Action
{
    const ADMIN_RESOURCE = 'Forever_DemoInstaller::demo';

    public function __construct(
        Context $context,
        private JsonFactory $resultJsonFactory,
        private DemoManager $demoManager
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $code   = (string)$this->getRequest()->getParam('code');
        $store  = (int)$this->getRequest()->getParam('store', 0);
        try {
            $context = $this->demoManager->import($code, $store);
            return $result->setData([
                'success'  => true,
                'messages' => $context->getMessages(),
            ]);
        } catch (\Throwable $e) {
            return $result->setData(['success' => false, 'messages' => [$e->getMessage()]]);
        }
    }
}
