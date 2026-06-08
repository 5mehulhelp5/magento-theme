<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\AuthenticationPopUp\Block\Catalog\Product\View\AddTo;

use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Product view wishlist block
 *
 * @api
 * @since 100.1.1
 */
class Wishlist extends View
{
    /**
     * @param Context $context
     * @param EncoderInterface $urlEncoder
     * @param JsonEncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param ProductHelper $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param WishlistHelper $wishlistHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $urlEncoder,
        JsonEncoderInterface $jsonEncoder,
        StringUtils $string,
        ProductHelper $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        private readonly WishlistHelper $wishlistHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    /**
     * Return wishlist widget options json
     *
     * @return string
     * @since 100.1.1
     */
    public function getWishlistOptionsJson(): string
    {
        return $this->_jsonEncoder->encode($this->getWishlistOptions());
    }

    /**
     * Return wishlist widget options
     *
     * @return array
     * @since 100.1.1
     */
    public function getWishlistOptions(): array
    {
        return ['productType' => $this->escapeHtml($this->getProduct()->getTypeId())];
    }

    /**
     * Return wishlist add params
     *
     * @return string
     * @since 100.1.1
     */
    public function getWishlistParams(): string
    {
        $product = $this->getProduct();
        return $this->wishlistHelper->getAddParams($product);
    }

    /**
     * Check whether the wishlist is allowed
     *
     * @return bool
     * @since 100.1.1
     */
    public function isWishListAllowed(): bool
    {
        return (bool) $this->wishlistHelper->isAllow();
    }
}
