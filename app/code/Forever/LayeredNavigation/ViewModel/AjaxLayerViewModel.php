<?php

declare(strict_types=1);

namespace Forever\LayeredNavigation\ViewModel;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class AjaxLayerViewModel implements ArgumentInterface
{
    /** @var ScopeConfigInterface */
    protected $scopeconfig;

    /** @var SessionFactory */
    protected $session;

    /** @var DataObjectFactory */
    protected $dataobject;

    /** @var Http */
    protected $request;

    public function __construct(
        ScopeConfigInterface $scopeconfig,
        SessionFactory $session,
        DataObjectFactory $dataobject,
        Http $request
    ) {
        $this->scopeconfig = $scopeconfig;
        $this->session = $session;
        $this->dataobject = $dataobject;
        $this->request = $request;
    }

    public function getScopeconfig($value): bool
    {
        return (bool)$this->scopeconfig->getValue($value, ScopeInterface::SCOPE_STORE);
    }

    public function getLayerConfiguration($filters): string
    {
        $params = $this->request->getParams();
        $filterParams = [];
        foreach ($params as $key => $param) {
            if ($key === 'amp;dimbaar') {
                continue;
            }
            $filterParams[$this->escapeParam($key)] = $this->escapeParam($param);
        }

        $config = $this->dataobject->create()->setData([
            'active' => array_keys($filterParams),
            'params' => $filterParams,
            'isCustomerLoggedIn' => $this->session->create()->isLoggedIn()
        ]);

        return (string)json_encode($config->getData(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    private function escapeParam($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'escapeParam'], $value);
        }

        return htmlentities((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
