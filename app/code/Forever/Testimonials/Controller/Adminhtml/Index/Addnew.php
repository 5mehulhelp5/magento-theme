<?php

namespace Forever\Testimonials\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Forever\Testimonials\Model\TestimonialsFactory;
use Magento\Framework\Controller\ResultFactory;

class Addnew extends Action implements HttpGetActionInterface
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
    private $entityFactory;

    /**
     * @param Context $context
     * @param TestimonialsFactory $entityFactory
     */
    public function __construct(
        Context $context,
        TestimonialsFactory $entityFactory
    ) {
        parent::__construct($context);
        $this->entityFactory = $entityFactory;
    }

    /**
     * Create new Entity
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->entityFactory->create();
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Forever_Testimonials::first_level_menu');

        $title = "Testimonials Information";

        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
