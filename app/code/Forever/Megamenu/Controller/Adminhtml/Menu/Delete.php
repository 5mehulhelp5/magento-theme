<?php

namespace Forever\Megamenu\Controller\Adminhtml\Menu;

class Delete extends \Forever\Megamenu\Controller\Adminhtml\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        //megamenu_id to id
        $id = $this->getRequest()->getParam('id');
        try {
            $item = $this->_megamenuFactory->create()->setId($id);
            $item->delete();
            $this->messageManager->addSuccessMessage(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
