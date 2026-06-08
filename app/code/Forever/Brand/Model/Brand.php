<?php

namespace Forever\Brand\Model;

class Brand extends \Magento\Framework\Model\AbstractModel
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'brand_records';

    /**
     * @var string
     */
    protected $_cacheTag = 'brand_records';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'brand_records';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Forever\Brand\Model\ResourceModel\Brand::class);
    }
}
