<?php

namespace Forever\Team\Model;

class Team extends \Magento\Framework\Model\AbstractModel
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'team_records';

    /**
     * @var string
     */
    protected $_cacheTag = 'team_records';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'team_records';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Forever\Team\Model\ResourceModel\Team::class);
    }
}
