<?php

namespace Forever\Megamenu\Model;

class Megamenu extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Forever\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory
     */
    protected $megamenuCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Forever\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $megamenuCollectionFactory
     * @param \Forever\Megamenu\Model\ResourceModel\Megamenu $resource
     * @param \Forever\Megamenu\Model\ResourceModel\Megamenu\Collection $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Forever\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $megamenuCollectionFactory,
        \Forever\Megamenu\Model\ResourceModel\Megamenu $resource,
        \Forever\Megamenu\Model\ResourceModel\Megamenu\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->megamenuCollectionFactory = $megamenuCollectionFactory;
    }
}
