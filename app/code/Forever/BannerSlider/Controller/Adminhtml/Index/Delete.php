<?php
namespace Forever\BannerSlider\Controller\Adminhtml\Index;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var Forever\BannerSlider\Model\Banner
     */
    protected $modelBanner;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Forever\BannerSlider\Model\Banner $bannerFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Forever\BannerSlider\Model\Banner $bannerFactory
    ) {
        parent::__construct($context);
        $this->modelBanner = $bannerFactory;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Forever_BannerSlider::index_delete');
    }
   
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $bannerFactory = $this->modelBanner;
                $bannerFactory->load($id);
                $bannerFactory->delete();
                $this->messageManager->addSuccessMessage(__('Record deleted successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('Record does not exist.'));
        return $resultRedirect->setPath('*/*/');
    }
}
