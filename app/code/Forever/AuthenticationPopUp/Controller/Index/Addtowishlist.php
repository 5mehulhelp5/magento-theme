<?php

namespace Forever\AuthenticationPopUp\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Forever\AuthenticationPopUp\Helper\Data as AjaxLoginHelper;

class Addtowishlist implements ActionInterface, HttpGetActionInterface
{
    /**
     * @param RequestInterface $request
     * @param Session $customerSession
     * @param WishlistFactory $wishlistRepository
     * @param ProductRepositoryInterface $productRepository
     * @param JsonFactory $jsonFactory
     * @param AjaxLoginHelper $helper
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly Session $customerSession,
        private readonly WishlistFactory $wishlistRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly JsonFactory $jsonFactory,
        private readonly AjaxLoginHelper $helper
    ) {
    }

    /**
     * Display Status, Redirect Page and show Message.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $customerId = $this->customerSession->getCustomer()->getId();

        if (!$customerId) {
            $jsonData = [
                'status'   => 400,
                'redirect' => 0,
                'message'  => 'Customer not logged in.'
            ];
            return $this->jsonFactory->create()->setData($jsonData);
        }

        $productId = $this->request->getParam('productId');

        try {
            $product = $this->productRepository->getById((int)$productId);
        } catch (NoSuchEntityException $e) {
            $jsonData = [
                'status'   => 404,
                'redirect' => 0,
                'message'  => 'Product not found.'
            ];
            return $this->jsonFactory->create()->setData($jsonData);
        }

        $wishlist = $this->wishlistRepository->create()->loadByCustomerId($customerId, true);
        $wishlist->addNewItem($product);
        $wishlist->save();

        $jsonData = [
            'status'   => 200,
            'redirect' => 1,
            'message'  => 'Added to wishlist'
        ];

        return $this->jsonFactory->create()->setData($jsonData);
    }
}
