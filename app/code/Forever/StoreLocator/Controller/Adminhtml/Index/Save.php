<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Forever\StoreLocator\Model\StoreFactory;
use Forever\StoreLocator\Model\ResourceModel\Store as StoreResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session.
     */
    const ADMIN_RESOURCE = 'Forever_StoreLocator::store';

    /**
     * @var StoreFactory
     */
    private StoreFactory $storeFactory;

    /**
     * @var StoreResource
     */
    private StoreResource $storeResource;

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @param Context       $context
     * @param StoreFactory  $storeFactory
     * @param StoreResource $storeResource
     * @param PageFactory   $resultPageFactory
     */
    public function __construct(
        Context $context,
        StoreFactory $storeFactory,
        StoreResource $storeResource,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->storeFactory      = $storeFactory;
        $this->storeResource     = $storeResource;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data           = $this->getRequest()->getPost();

        if ($data) {
            $id         = $this->getRequest()->getParam('store_id');
            $storeModel = $this->storeFactory->create();

            if ($id) {
                $this->storeResource->load($storeModel, $id);
            }

            $storeModel->setName($data['name']);
            $storeModel->setUrlKey($data['url_key']);
            $storeModel->setStreet($data['street']);
            $storeModel->setCity($data['city']);
            $storeModel->setState($data['state']);
            $storeModel->setZip($data['zip']);
            $storeModel->setCountry($data['country']);
            $storeModel->setStatus($data['status']);
            $storeModel->setDescription($data['description']);
            $storeModel->setLatitude($data['latitude']);
            $storeModel->setLongitude($data['longitude']);

            try {
                $this->storeResource->save($storeModel);
                $this->messageManager->addSuccessMessage(__('Row data has been successfully saved.'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['store_id' => $storeModel->getStoreId(), '_current' => true]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the Data.'));
            }

            return $resultRedirect->setPath(
                '*/*/edit',
                ['store_id' => $this->getRequest()->getParam('store_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
