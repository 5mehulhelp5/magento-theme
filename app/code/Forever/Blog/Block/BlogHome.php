<?php

namespace Forever\Blog\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Forever\Blog\Model\ResourceModel\Blog\CollectionFactory as BlogCollectionFactory;

class BlogHome extends Template
{
    const XML_PATH_EMAIL_RECIPIENT = 'blog/general/enable';

    /**
     * @var BlogCollectionFactory
     */
    protected $blogCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepos;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $helperImageFactory;

    /**
     * @param Template\Context $context
     * @param BlogCollectionFactory $blogCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storManager
     * @param \Magento\Framework\View\Asset\Repository $assetRepos
     * @param \Magento\Catalog\Helper\ImageFactory $helperImageFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        BlogCollectionFactory $blogCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storManager,
        \Magento\Framework\View\Asset\Repository $assetRepos,
        \Magento\Catalog\Helper\ImageFactory $helperImageFactory,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blogCollectionFactory = $blogCollectionFactory;
        $this->storManager = $storManager;
        $this->scopeConfig = $scopeConfig;
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;
    }

    /**
     * @return \Forever\Blog\Model\ResourceModel\Blog\Collection
     */
    public function getBlogCollection()
    {
        $now = new \DateTime();
        $collection = $this->blogCollectionFactory->create()
            ->addFieldToFilter('publish_time', ['lteq' => $now->format('Y-m-d H:i:s')])
            ->setPageSize(6)
            ->addFieldToFilter('status', 1)
            ->setOrder('publish_time', 'DESC');

        return $collection;
    }

    /**
     * @param string $dateTime
     * @return string
     */
    public function getBlogDate($dateTime)
    {
        $date = strtotime($dateTime);
        return date('d/m/Y', $date);
    }

    /**
     * @param string $image
     * @return string
     */
    public function getBlogImage($image)
    {
        $directory = 'blog/image/';
        $mediapath = $this->storManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        return $mediapath . $directory . $image;
    }

    /**
     * @param string $shortContent
     * @return string
     */
    public function getBlogShortContent($shortContent)
    {
        if (strlen($shortContent) > 110) {
            return substr(strip_tags($shortContent), 0, 110) . '...';
        } else {
            return strip_tags($shortContent);
        }
    }

    /**
     * @param string $viewUrlKey
     * @return string
     */
    public function getViewUrl($viewUrlKey)
    {
        $baseUrl = $this->storManager->getStore()->getBaseUrl();
        return $baseUrl . 'blog/index/view/' . $viewUrlKey;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);
    }

    /**
     * @return string
     */
    public function getPlaceHolderImage()
    {
        $imagePlaceholder = $this->helperImageFactory->create();
        return $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('small_image'));
    }
}
