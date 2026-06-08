<?php

namespace Forever\Blog\Block;

use Magento\Framework\View\Element\Template;
use Forever\Blog\Model\BlogFactory;
use Forever\Blog\Model\ResourceModel\Blog as BlogResource;

class View extends Template
{
    /**
     * @var BlogFactory
     */
    protected $blogFactory;

    /**
     * @var BlogResource
     */
    protected $blogResource;

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
     * @param BlogFactory $blogFactory
     * @param BlogResource $blogResource
     * @param \Magento\Framework\View\Asset\Repository $assetRepos
     * @param \Magento\Catalog\Helper\ImageFactory $helperImageFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        BlogFactory $blogFactory,
        BlogResource $blogResource,
        \Magento\Framework\View\Asset\Repository $assetRepos,
        \Magento\Catalog\Helper\ImageFactory $helperImageFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blogFactory = $blogFactory;
        $this->blogResource = $blogResource;
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;
    }

    /**
     * @param string $urlKey
     * @return \Forever\Blog\Model\Blog
     */
    public function getPost($urlKey)
    {
        $model = $this->blogFactory->create();
        $this->blogResource->load($model, $urlKey, 'url_key');
        return $model;
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
