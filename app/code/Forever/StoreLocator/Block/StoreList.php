<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Block;

use Magento\Framework\View\Element\Template;
use Forever\StoreLocator\Model\ResourceModel\Store\CollectionFactory;

/**
 * @Class StoreList
 */
class StoreList extends Template
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param Template\Context  $context
     * @param CollectionFactory $collectionFactory
     * @param array             $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get active store collection.
     *
     * @return \Forever\StoreLocator\Model\ResourceModel\Store\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', ['eq' => '1']);
    }
}
