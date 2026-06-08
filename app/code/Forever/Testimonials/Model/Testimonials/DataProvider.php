<?php

namespace Forever\Testimonials\Model\Testimonials;

use Forever\Testimonials\Model\ResourceModel\Testimonials\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $entityCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        StoreManagerInterface $storeManager,
        CollectionFactory $entityCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $entityCollectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $this->loadedData = [];

        foreach ($items as $item) {
            $itemData = $item->getData();
            if (isset($itemData['image']) && $itemData['image']) {
                $imageName = $itemData['image'];

                $itemData['image'] = [
                    [
                        'name' => $imageName,
                        'url'  => $this->storeManager->getStore()
                            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                            . 'testimonials/image/' . $imageName,
                    ],
                ];
            }
            $this->loadedData[$item->getId()] = $itemData;
        }

        return $this->loadedData;
    }
}
