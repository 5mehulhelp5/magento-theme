<?php

namespace Forever\Blog\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
 
class TagActions extends Column
{
    const URL_PATH_EDIT = 'blog/tag/edit';
    const URL_PATH_DELETE = 'blog/tag/delete';

    /**
     * @var $urlBuilder
     */
    protected $urlBuilder;

    /**
     * @var $editUrl
     */
    private $editUrl;

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     * @param [type]             $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @return prepareDataSource
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
 
                if (isset($item['tag_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_EDIT,
                            [
                                    'tag_id' => $item['tag_id']
                                ]
                        ),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_DELETE,
                            [
                                    'tag_id' => $item['tag_id']
                                ]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'message' => __('Are you sure you wan\'t to delete a record?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
