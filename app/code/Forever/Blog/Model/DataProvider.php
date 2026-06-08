<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Model;

use Forever\Blog\Model\ResourceModel\Blog\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collection;
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
        CollectionFactory $contactCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $contactCollectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
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
        foreach ($items as $blog) {
            $itemData = $blog->getData();
            if (isset($itemData['blog_image'])) {
                $imageName = $itemData['blog_image'];
            
                $itemData['blog_image'] = [
                    [
                        'name' => $imageName,
                        'url' => $this->storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                        . 'blog/image/' . $itemData['blog_image'],
                    ],
                ];
            }
            $this->loadedData[$blog->getBlogId()] = $itemData;
        }
        return $this->loadedData;
    }
}
