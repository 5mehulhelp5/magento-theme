<?php

namespace Forever\Megamenu\Ui\Component\Listing\Megamenu\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Action extends Column
{
    const ROW_EDIT_URL = 'megamenu/menu/addrow';
    
    /**
     * @var urlbuilder
     */
    protected $urlBuilder;

    /**
     * @var editurl
     */
    private $editUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::ROW_EDIT_URL
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Return preparedatasource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                 $name = $this->getData('name');
                 //megamenu_id to id
                if (isset($item['id'])) {
                    //megamenu_id to id
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            $this->editUrl,
                            ['id' => $item['id']]
                        ),
                        'label' => __('Edit'),
                    ];
                }
            }
        }
        return $dataSource;
    }
}
