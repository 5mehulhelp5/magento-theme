<?php

namespace Forever\Brand\Model\ResourceModel\Brand;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'brand_entity_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'entity_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Forever\Brand\Model\Brand::class,
            \Forever\Brand\Model\ResourceModel\Brand::class
        );
    }
}
