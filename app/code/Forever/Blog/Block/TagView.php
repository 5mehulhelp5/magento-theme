<?php

namespace Forever\Blog\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Forever\Blog\Model\ResourceModel\Blog\CollectionFactory as BlogCollectionFactory;
use Forever\Blog\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;

class TagView extends Template
{
    /**
     * @var BlogCollectionFactory
     */
    protected $blogCollectionFactory;

    /**
     * @var TagCollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storManager;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $helperImageFactory;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepos;

    /**
     * @param Template\Context $context
     * @param BlogCollectionFactory $blogCollectionFactory
     * @param TagCollectionFactory $tagCollectionFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepos
     * @param \Magento\Catalog\Helper\ImageFactory $helperImageFactory
     * @param StoreManagerInterface $storManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        BlogCollectionFactory $blogCollectionFactory,
        TagCollectionFactory $tagCollectionFactory,
        \Magento\Framework\View\Asset\Repository $assetRepos,
        \Magento\Catalog\Helper\ImageFactory $helperImageFactory,
        StoreManagerInterface $storManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blogCollectionFactory = $blogCollectionFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;
        $this->storManager = $storManager;
    }

    /**
     * @param string $tagviewUrlKey
     * @return \Forever\Blog\Model\ResourceModel\Blog\Collection
     */
    public function getTagPostList($tagviewUrlKey)
    {
        $tagId = $this->getTagIdByTitle($tagviewUrlKey);
        $blogViewColl = $this->blogCollectionFactory->create()
            ->addFieldToFilter('status', 1);
        foreach ($blogViewColl as $key => $val) {
            $tagArr = explode(", ", $val->getTags());
            if (!in_array($tagId, $tagArr)) {
                $blogViewColl->removeItemByKey($val->getId());
            }
        }
        return $blogViewColl;
    }

    /**
     * @param string $tagviewUrlKey
     * @return int|null
     */
    public function getTagIdByTitle($tagviewUrlKey)
    {
        $getTagColl = $this->tagCollectionFactory->create();
        foreach ($getTagColl as $key => $value) {
            $title = strtolower($value->getTitle());
            $title = str_replace(' ', '-', $title);
            if ($title == $tagviewUrlKey) {
                return $value->getId();
            }
        }
        return null;
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
     * @return string
     */
    public function getPlaceHolderImage()
    {
        $imagePlaceholder = $this->helperImageFactory->create();
        return $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('small_image'));
    }
}
