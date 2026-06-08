<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Forever\Blog\Model\ResourceModel\Tag\CollectionFactory;

class TagDataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $tagCollectionFactory;
    /**
     * @var array
     */
    protected $loadedData;
    
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $tagCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $tagCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        return $this->getCollection()->toArray();
    }
}
