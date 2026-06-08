<?php

namespace Forever\BannerSlider\Block;
 
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Element\Template;
use Magento\Framework\UrlInterface;
 
class BannerSlider extends Template
{
     /**
      * @var Forever\BannerSlider\Model\Banner
      **/
     protected $bannerFactory;

     /**
      * @var Magento\Store\Model\StoreManagerInterface
      */
      protected $storeManager;

      /**
       * @var Psr\Log\LoggerInterface
       */
      protected $logger;

     /**
      * @param \Magento\Framework\View\Element\Template\Context $context
      * @param \Forever\BannerSlider\Model\Banner $bannerFactory
      * @param \Magento\Store\Model\StoreManagerInterface $storeManager
      * @param \Psr\Log\LoggerInterface $logger
      **/
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Forever\BannerSlider\Model\BannerFactory $bannerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ) {
          parent::__construct($context);
          $this->bannerFactory = $bannerFactory;
          $this->storeManager = $storeManager;
          $this->logger = $logger;
    }

     /**
      *
      * it will return all the banners
      *
      * @return mixed $bannerCollection
      **/
    public function getBannerCollection()
    {
        try {
            $post = $this->bannerFactory->create();
            $collections = $post->getCollection()->addFieldToFilter('status', 1);
            return $collections;
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        return false;
    }

    /**
     * get media URL of current store
     *
     * @return string
     */
    public function getMediaUrl()
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        return false;
    }
}
