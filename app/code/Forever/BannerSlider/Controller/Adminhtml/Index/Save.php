<?php
namespace Forever\BannerSlider\Controller\Adminhtml\Index;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var Forever\BannerSlider\Model\Banner
     */
    protected $boardmodel;
    
    /**
     * @var Magento\Backend\Model\Session
     */
    protected $adminsession;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Forever\BannerSlider\Model\Banner $boardmodel
     * @param \Magento\Backend\Model\Session $adminsession
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Forever\BannerSlider\Model\Banner $boardmodel,
        \Magento\Backend\Model\Session $adminsession
    ) {
        parent::__construct($context);
        $this->boardmodel = $boardmodel;
        $this->adminsession = $adminsession;
    }
    
    /**
     * Save banner record action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($data) {
            if (empty($data['id'])) {
                 $data['id'] = null;
            }
            if (isset($data['sliderimage'])
                && isset($data['sliderimage'][0])
                && isset($data['sliderimage'][0]['name'])
            ) {
                $imagename=$data['sliderimage'][0]['name'];
                $data['sliderimage']= $imagename;
            }
            $this->boardmodel->setData($data);
            try {
                $this->boardmodel->save();
                $this->messageManager->addSuccessMessage(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    } else {
                        if ($id) {
                            return $resultRedirect->setPath('*/*/edit', ['id' => $this->boardmodel->getId()]);
                        } else {
                            return $resultRedirect->setPath('*/index/index');
                        }
                    }
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }
            $this->_getSession()->setFormData($data);
        }
        if ($id) {
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        } else {
            return $resultRedirect->setPath('*/index/index');
        }
        return $resultRedirect->setPath('*/*/');
    }
}
