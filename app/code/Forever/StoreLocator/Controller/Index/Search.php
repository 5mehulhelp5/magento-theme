<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Forever\StoreLocator\Model\ResourceModel\Store\CollectionFactory;

class Search implements HttpPostActionInterface, CsrfAwareActionInterface
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param RequestInterface  $request
     * @param CollectionFactory $collectionFactory
     * @param JsonFactory       $resultJsonFactory
     */
    public function __construct(
        RequestInterface $request,
        CollectionFactory $collectionFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Bypass CSRF validation for this AJAX search endpoint.
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Bypass CSRF validation for this AJAX search endpoint.
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Execute search and return JSON result.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $ajaxData   = $this->request->getParam('search');

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter(
                ['name', 'street'],
                [
                    ['like' => '%' . $ajaxData . '%'],
                    ['like' => '%' . $ajaxData . '%'],
                ]
            );

        $response = [];
        foreach ($collection as $value) {
            $response[] = [
                $value->getName() . ' ' . $value->getStreet() . ' ' . $value->getCity()
                    . ' , ' . $value->getState() . ' ,' . $value->getCountry(),
                $value->getLatitude(),
                $value->getLongitude(),
                $value->getStoreId(),
            ];
        }

        return $resultJson->setData($response);
    }
}
