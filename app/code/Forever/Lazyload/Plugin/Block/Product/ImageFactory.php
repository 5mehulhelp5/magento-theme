<?php
namespace Forever\Lazyload\Plugin\Block\Product;

use Magento\Catalog\Block\Product\Image;

class ImageFactory
{
    const IMAGE_BORDER_TEMPLATE = 'Forever_Lazyload::product/image_with_borders.phtml';

    /**
     * @var \Forever\Lazyload\Helper\Filter
     */
    protected $filterHelper;

    /**
     * @param \Forever\Lazyload\Helper\Filter $filterHelper
     */
    public function __construct(
        \Forever\Lazyload\Helper\Filter $filterHelper
    ) {
        $this->filterHelper = $filterHelper;
    }

    /**
     * After plugin for ImageFactory::create()
     *
     * @param \Magento\Catalog\Block\Product\ImageFactory $subject
     * @param Image $result
     * @return Image
     */
    public function afterCreate(
        \Magento\Catalog\Block\Product\ImageFactory $subject,
        Image $result
    ): Image {
        if ($this->filterHelper->isEnable()) {
            $result->setTemplate(self::IMAGE_BORDER_TEMPLATE);
        }
        return $result;
    }
}
