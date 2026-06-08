<?php

namespace Forever\BannerSlider\Model;

use Magento\Framework\Model\AbstractModel;
use Forever\BannerSlider\Model\ResourceModel\Banner as BannerResourceModel;

class Banner extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(BannerResourceModel::class);
    }
}
