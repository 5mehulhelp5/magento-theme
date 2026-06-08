<?php

namespace Forever\Megamenu\Model;
 
use Forever\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory;
 
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var loadeddata
     */
    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $brandCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $brandCollection->create();
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
 
    /**
     * Return loadeddata
     *
     * @return loadeddata
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $value) {
            $val = $value->getData();

            // image start
            if (isset($val['image'])) {
                $imageName = $val['image'];
                $val['image'] = [
                    [
                        'name' => $imageName,
                        'url' => $this->getMediaUrl() . $imageName
                    ],
                ];
            } else {
                $val['image'] = null;
            }
            // image end

            $demo = $val['stores'];
            explode(',', $val['stores']);
            $val['stores'] = $value->getStores();
            //megamenu_id to id
            $this->loadedData[$value['id']] = $val;
        }
        return $this->loadedData;
    }

    /**
     * Return media url
     *
     * @return array
     */
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'forever/customcategoryimg/image/';
    }
}
