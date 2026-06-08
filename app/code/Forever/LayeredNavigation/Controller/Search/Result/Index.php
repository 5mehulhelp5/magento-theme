<?php

declare(strict_types=1);

namespace Forever\LayeredNavigation\Controller\Search\Result;

use Forever\LayeredNavigation\ViewModel\AjaxLayerViewModel;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer;
use Magento\Catalog\Model\Session;
use Magento\CatalogSearch\Helper\Data as CatalogSearchHelper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Search\Model\PopularSearchTerms;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    public const CONFIG_ENABLE = 'layered_navigation/general/ajax_enable';
    public const DEFAULT_NO_RESULT_HANDLE = 'catalogsearch_result_index_noresults';

    /** @var Session */
    protected $_catalogSession;

    /** @var StoreManagerInterface */
    protected $_storeManager;

    /** @var QueryFactory */
    private $_queryFactory;

    /** @var Resolver */
    private $layerResolver;

    /** @var JsonHelper */
    private $jsonHelper;

    /** @var AjaxLayerViewModel */
    private $moduleViewmodel;

    /** @var ToolbarMemorizer */
    private $toolbarMemorizer;

    public function __construct(
        Context $context,
        Session $catalogSession,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        JsonHelper $jsonHelper,
        AjaxLayerViewModel $moduleViewmodel,
        ?ToolbarMemorizer $toolbarMemorizer = null
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_catalogSession = $catalogSession;
        $this->_queryFactory = $queryFactory;
        $this->layerResolver = $layerResolver;
        $this->jsonHelper = $jsonHelper;
        $this->moduleViewmodel = $moduleViewmodel;
        $this->toolbarMemorizer = $toolbarMemorizer
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(ToolbarMemorizer::class);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function execute()
    {
        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
        $query = $this->_queryFactory->get();
        $storeId = (int)$this->_storeManager->getStore()->getId();
        $query->setStoreId($storeId);
        $queryText = (string)$query->getQueryText();

        if ($queryText === '') {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
            return;
        }

        if ((int)$this->_request->getParam(Toolbar::PAGE_PARM_NAME) < 0) {
            $this->getResponse()->setRedirect(
                $this->_url->getUrl('*/*', ['_current' => true, '_query' => [Toolbar::PAGE_PARM_NAME => null]])
            );
            return;
        }

        /** @var CatalogSearchHelper $catalogSearchHelper */
        $catalogSearchHelper = $this->_objectManager->get(CatalogSearchHelper::class);
        $getAdditionalRequestParameters = $this->getRequest()->getParams();
        unset($getAdditionalRequestParameters[QueryFactory::QUERY_VAR_NAME]);

        $handles = null;
        if ((int)$query->getNumResults() === 0) {
            $this->_view->getPage()->initLayout();
            $handles = $this->_view->getLayout()->getUpdate()->getHandles();
            $handles[] = static::DEFAULT_NO_RESULT_HANDLE;
        }

        if (!$this->isAjaxLayerRequest() && $this->shouldRedirectOnToolbarAction()) {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
            return;
        }

        if (empty($getAdditionalRequestParameters)
            && $this->_objectManager->get(PopularSearchTerms::class)->isCacheable($queryText, $storeId)
        ) {
            $this->getCacheableResult($catalogSearchHelper, $query, $handles);
        } else {
            $this->getNotCacheableResult($catalogSearchHelper, $query, $handles);
        }
    }

    private function getCacheableResult(CatalogSearchHelper $catalogSearchHelper, $query, ?array $handles): void
    {
        if (!$catalogSearchHelper->isMinQueryLength()) {
            $redirect = $query->getRedirect();
            if ($redirect && $this->_url->getCurrentUrl() !== $redirect) {
                $this->getResponse()->setRedirect($redirect);
                return;
            }
        }

        $catalogSearchHelper->checkNotes();
        $this->renderSearchLayout($handles, false);
    }

    private function getNotCacheableResult(CatalogSearchHelper $catalogSearchHelper, $query, ?array $handles): void
    {
        if ($catalogSearchHelper->isMinQueryLength()) {
            $query->setId(0)->setIsActive(1)->setIsProcessed(1);
        } else {
            $query->saveIncrementalPopularity();
            $redirect = $query->getRedirect();
            if ($redirect && $this->_url->getCurrentUrl() !== $redirect) {
                $this->getResponse()->setRedirect($redirect);
                return;
            }
        }

        $catalogSearchHelper->checkNotes();
        $this->renderSearchLayout($handles, true);
    }

    /**
     * @throws LocalizedException
     */
    private function renderSearchLayout(?array $handles, bool $noCache): void
    {
        $this->_view->loadLayout($handles);
        if ($noCache) {
            $this->getResponse()->setNoCacheHeaders();
        }

        if ($this->isAjaxLayerRequest()) {
            $layout = $this->_view->getLayout();
            $navigation = $layout->getBlock('catalogsearch.leftnav');
            $products = $layout->getBlock('layer.category.products') ?: $layout->getBlock('search.result');
            $result = [
                'products' => $products ? $products->toHtml() : '',
                'navigation' => $navigation ? $navigation->toHtml() : ''
            ];
            $this->getResponse()->representJson($this->jsonHelper->jsonEncode($result));
            return;
        }

        $this->_view->renderLayout();
    }

    private function isAjaxLayerRequest(): bool
    {
        return (bool)$this->moduleViewmodel->getScopeconfig(self::CONFIG_ENABLE)
            && ($this->getRequest()->isAjax() || (bool)$this->getRequest()->getParam('isAjax'));
    }

    private function shouldRedirectOnToolbarAction(): bool
    {
        $params = $this->getRequest()->getParams();

        return $this->toolbarMemorizer->isMemorizingAllowed()
            && empty(array_intersect([
                Toolbar::ORDER_PARAM_NAME,
                Toolbar::DIRECTION_PARAM_NAME,
                Toolbar::MODE_PARAM_NAME,
                Toolbar::LIMIT_PARAM_NAME
            ], array_keys($params))) === false;
    }
}
