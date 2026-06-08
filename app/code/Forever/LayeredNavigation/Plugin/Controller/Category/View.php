<?php

declare(strict_types=1);

namespace Forever\LayeredNavigation\Plugin\Controller\Category;

use Forever\LayeredNavigation\Helper\Data as LayerData;
use Forever\LayeredNavigation\ViewModel\AjaxLayerViewModel;
use Magento\Catalog\Controller\Category\View as CategoryView;

class View
{
    public const CONFIG_ENABLE = 'layered_navigation/general/ajax_enable';

    /** @var AjaxLayerViewModel */
    protected $moduleViewmodel;

    public function __construct(AjaxLayerViewModel $moduleViewmodel)
    {
        $this->moduleViewmodel = $moduleViewmodel;
    }

    public function afterExecute(CategoryView $action, $page)
    {
        if ($this->moduleViewmodel->getScopeconfig(self::CONFIG_ENABLE)
            && ($action->getRequest()->isAjax() || $action->getRequest()->getParam('isAjax'))
            && !$action->getRequest()->getParam('ajaxscroll')
            && $page
        ) {
            $navigation = $page->getLayout()->getBlock('catalog.leftnav');
            $products = $page->getLayout()->getBlock('layer.category.products') ?: $page->getLayout()->getBlock('category.products');
            $result = [
                'products' => $products ? $products->toHtml() : '',
                'navigation' => $navigation ? $navigation->toHtml() : ''
            ];
            $action->getResponse()->representJson(LayerData::jsonEncode($result));
        }

        return $page;
    }
}
