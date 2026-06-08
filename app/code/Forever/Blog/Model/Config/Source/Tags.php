<?php

namespace Forever\Blog\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Forever\Blog\Model\ResourceModel\Tag\CollectionFactory;

class Tags implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array("value" => "<value>", "label"=> "<label>"), ...)
     */
    public function toOptionArray()
    {
        $tagCollection = $this->collectionFactory->create()
            ->addFieldToFilter('status', ['eq' => '1']);
        $options = [];
        foreach ($tagCollection as $tag) {
            $options[] = [
                'label' => $tag->getTitle(),
                'value' => $tag->getId()
            ];
        }
        return $options;
    }
}
