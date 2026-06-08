<?php

namespace Forever\Megamenu\Controller\Adminhtml\Menu;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Forever\Megamenu\Controller\Adminhtml\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data = $this->getRequest()->getPostValue()) {
            $model = $this->_megamenuFactory->create();
            $storeViewId = $this->getRequest()->getParam('store');

            if ($id = $this->getRequest()->getParam('id')) {
                $model->load($id);
            }
            if (isset($data['image'][0]['name'])) {
                $data['image'] = $data['image'][0]['name'];
            } else {
                $data['image'] = '';
            }
            if (isset($data['stores'])) {
                $data['stores'] = implode(',', $data['stores']);
            }
            $model->setData($data)->setStoreViewId($storeViewId);

            try {
                $model->save();

                $this->messageManager->addSuccessMessage(__('The Menu menu has been saved.'));
                $this->_getSession()->setFormData(false);

                //megamenu_id to id
                if ($this->getRequest()->getParam('back') === 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id' => $model->getId(),
                            '_current' => true,
                            'store' => $storeViewId,
                            'current_id' => $this->getRequest()->getParam('current_id'),
                            'saveandclose' => $this->getRequest()->getParam('saveandclose'),
                        ]
                    );
                } elseif ($this->getRequest()->getParam('back') === 'new') {
                    return $resultRedirect->setPath(
                        '*/*/new',
                        ['_current' => true]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the megamenu.'));
            }

            $this->_getSession()->setFormData($data);

            //megamenu_id to id
            return $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $this->getRequest()->getParam('id')]
            );
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
