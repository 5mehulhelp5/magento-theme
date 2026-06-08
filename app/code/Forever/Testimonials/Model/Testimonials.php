<?php

namespace Forever\Testimonials\Model;

use Forever\Testimonials\Model\ResourceModel\Testimonials as TestimonialsModel;

class Testimonials extends \Magento\Framework\Model\AbstractModel
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'testimonials_records';

    /**
     * @var string
     */
    protected $_cacheTag = 'testimonials_records';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'testimonials_records';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(TestimonialsModel::Class);
    }
}
