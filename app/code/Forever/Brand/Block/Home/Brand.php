<?php

namespace Forever\Brand\Block\Home;

use Magento\Framework\View\Element\Template;
use Forever\Brand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Store\Model\StoreManagerInterface;

class Brand extends Template
{
    /*
    * This label won't be displayed in the frontend block
    */
    const MAIN_LABEL = 'Default';

    const MODULE_ENABLE = 'brand/general/enable';

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    protected $storeManager;
    protected $mediaDirectory;
    protected $fileDriver;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        File $fileDriver,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->mediaDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->fileDriver = $fileDriver;
        $this->storeManager = $storeManager;
    }

    /**
     * Get All Questions
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getItems()
    {
        $questionCollection = $this->collectionFactory->create();
        $questionCollection->addFieldToFilter('main_table.status', 1)
        ->setOrder('main_table.orders', 'ASC')->setPageSize(4);

        return $questionCollection->getItems();
    }
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getBrandImageUrl($imageName)
    {
        $relativePath = $this->resolveBrandImagePath((string) $imageName);

        if (!$relativePath) {
            return '';
        }

        return $this->getMediaUrl() . $relativePath;
    }

    public function getConfigValue()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::MODULE_ENABLE, $storeScope);
    }

    private function resolveBrandImagePath(string $imageName): string
    {
        $imageName = trim($imageName);
        if ($imageName === '') {
            return '';
        }

        $filename = basename($imageName);
        $normalizedFilename = preg_replace('/_\d+(?=\.[^.]+$)/', '', $filename) ?: $filename;
        $candidates = [
            $imageName,
            'brand/image/' . $filename,
            'wysiwyg/forever/' . $filename,
        ];

        if ($normalizedFilename !== $filename) {
            $candidates[] = 'brand/image/' . $normalizedFilename;
            $candidates[] = 'wysiwyg/forever/' . $normalizedFilename;
        }

        foreach (array_unique($candidates) as $candidate) {
            $candidate = ltrim($candidate, '/');
            if ($candidate === '') {
                continue;
            }

            if ($this->fileDriver->isExists($this->mediaDirectory->getAbsolutePath($candidate))) {
                return $candidate;
            }
        }

        return '';
    }
}
