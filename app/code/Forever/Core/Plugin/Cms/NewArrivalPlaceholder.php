<?php

namespace Forever\Core\Plugin\Cms;

use Forever\Core\Block\CategoryList;
use Magento\Framework\View\LayoutInterface;
use Magento\Widget\Model\Template\Filter;

class NewArrivalPlaceholder
{
    private const PLACEHOLDER = '<!-- FOREVER_NEW_ARRIVAL_PRODUCTS -->';
    private const BLOCK_NAME = 'forever.home.newarrival.cms';

    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    public function afterFilter(Filter $subject, string $result): string
    {
        if (strpos($result, self::PLACEHOLDER) === false) {
            return $result;
        }

        return str_replace(self::PLACEHOLDER, $this->renderNewArrival(), $result);
    }

    private function renderNewArrival(): string
    {
        $this->configurePriceRenderer();

        $block = $this->layout->getBlock(self::BLOCK_NAME);
        if (!$block) {
            $block = $this->layout->createBlock(CategoryList::class, self::BLOCK_NAME);
        }

        if (!$block) {
            return '';
        }

        $block->setTemplate('Forever_Core::product-filter.phtml');

        return $block->toHtml();
    }

    private function configurePriceRenderer(): void
    {
        $priceRenderer = $this->layout->getBlock('product.price.render.default');
        if (!$priceRenderer) {
            return;
        }

        $priceRenderer->setData('price_render_handle', 'catalog_product_prices');
        $priceRenderer->setData('use_link_for_as_low_as', true);
    }
}
