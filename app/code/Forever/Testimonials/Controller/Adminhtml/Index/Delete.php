<?php

namespace Forever\Testimonials\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Forever\Testimonials\Model\TestimonialsFactory;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Forever_Testimonials::testimonials';

    /**
     * @var TestimonialsFactory
     */
    private $testimonialsFactory;

    /**
     * @param Context $context
     * @param TestimonialsFactory $testimonialsFactory
     */
    public function __construct(
        Context $context,
        TestimonialsFactory $testimonialsFactory
    ) {
        parent::__construct($context);
        $this->testimonialsFactory = $testimonialsFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $model = $this->testimonialsFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('Testimonials has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find entity to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
