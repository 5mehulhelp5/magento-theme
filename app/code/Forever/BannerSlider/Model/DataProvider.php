<?php
namespace Forever\BannerSlider\Model;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Forever\BannerSlider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $collection;

    protected $loadedData;

    /**
     * @var Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Forever\BannerSlider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory
     * @param array $meta = []
     * @param array $data = []
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Forever\BannerSlider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $bannerCollectionFactory->create();
        $this->request = $request;
        $this->storeManager = $storeManager;
    }
    
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $banner) {
            $_data = $banner->getData();
            if (isset($_data['sliderimage'])) {
                $imageName = $_data['sliderimage'];
                $_data['sliderimage'] = [
                    [
                        'name' => $imageName,
                        'url' => $this->storeManager->getStore()->getBaseUrl(
                            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                        ) . 'banner/' . $_data['sliderimage'],
                    ],
                ];
            }
            $this->loadedData[$banner->getId()] = $_data;
        }
        return $this->loadedData;
    }
}
