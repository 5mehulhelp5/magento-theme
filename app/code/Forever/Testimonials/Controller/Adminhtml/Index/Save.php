<?php

namespace Forever\Testimonials\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Forever\Testimonials\Model\TestimonialsFactory;
use Magento\Framework\View\Result\PageFactory;

class Save extends Action implements HttpPostActionInterface
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
    protected $_entityFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param TestimonialsFactory $entityFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        TestimonialsFactory $entityFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_entityFactory = $entityFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Save action
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $entityModel    = $this->_entityFactory->create();
        $data           = $this->getRequest()->getPost();

        try {
            if (!empty($data['id'])) {
                $entityModel->setId($data['id']);
            }
            $entityModel->setData('name', $data['name']);
            $entityModel->setData('status', $data['status']);
            $entityModel->setData('message', $data['message']);
            if (isset($data['image'][0]['name'])) {
                $entityModel->setData('image', $data['image'][0]['name']);
            } else {
                $entityModel->setData('image', null);
            }
            $entityModel->save();

            $this->messageManager->addSuccessMessage(__('The Testimonials has been saved.'));

            // check for `back` parameter
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'id' => $entityModel->getId(),
                        '_current' => true,
                        '_use_rewrite' => true
                    ]
                );
            }
            return $resultRedirect->setPath('*/*');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return $resultRedirect->setPath('*/*');
        }
    }
}
