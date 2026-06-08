<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\AuthenticationPopUp\Block\Catalog\Product\ProductList\Item\AddTo;

use Magento\Catalog\Block\Product\ProductList\Item\Block;
use Magento\Catalog\Block\Product\Context;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Add product to wishlist - Category/Listing page
 *
 * @api
 * @since 100.1.1
 */
class Wishlist extends Block
{
    /**
     * @param Context $context
     * @param WishlistHelper $wishlistHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly WishlistHelper $wishlistHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return WishlistHelper
     * @since 100.1.1
     */
    public function getWishlistHelper(): WishlistHelper
    {
        return $this->wishlistHelper;
    }
}
