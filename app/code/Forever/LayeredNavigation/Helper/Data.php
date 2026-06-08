<?php

declare(strict_types=1);

namespace Forever\LayeredNavigation\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;

class Data extends AbstractData
{
    public const CONFIG_MODULE_PATH = 'layered_navigation';
    public const FILTER_TYPE_LIST = 'list';

    public function ajaxEnabled($storeId = null)
    {
        return $this->getConfigGeneral('ajax_enable', $storeId) && $this->isModuleOutputEnabled();
    }

    public function getLayerConfiguration($filters)
    {
        $params = $this->_getRequest()->getParams();
        $filterParams = [];
        foreach ($params as $key => $param) {
            if ($key === 'amp;dimbaar') {
                continue;
            }
            $filterParams[$this->escapeParam($key)] = $this->escapeParam($param);
        }

        $config = new DataObject([
            'active' => array_keys($filterParams),
            'params' => $filterParams,
            'isCustomerLoggedIn' => $this->objectManager->create(Session::class)->isLoggedIn()
        ]);

        return self::jsonEncode($config->getData());
    }

    private function escapeParam($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'escapeParam'], $value);
        }

        return htmlentities((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
