<?php

namespace Forever\Team\Model\ResourceModel\Team;

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
    protected $_eventPrefix = 'team_entity_collection';

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
            \Forever\Team\Model\Team::class,
            \Forever\Team\Model\ResourceModel\Team::class
        );
    }
}
