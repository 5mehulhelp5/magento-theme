<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Model;

use Forever\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class TagDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collectionFactory;

    /**
     * @var loadedData
     */
    protected $loadedData;

     /**
      * @var storeManager
      */
    protected $storeManager;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return getData
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $tag) {
            $this->loadedData[$tag->getTagId()] = $tag->getData();
        }
        return $this->loadedData;
    }
}
