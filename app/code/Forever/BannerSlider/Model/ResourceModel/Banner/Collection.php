<?php

namespace Forever\BannerSlider\Model\ResourceModel\Banner;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Forever\BannerSlider\Model\Banner as BannerModel;
use Forever\BannerSlider\Model\ResourceModel\Banner as BannerResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(BannerModel::class, BannerResourceModel::class);
    }
}
