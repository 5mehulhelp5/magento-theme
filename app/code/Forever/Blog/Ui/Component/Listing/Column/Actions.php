<?php

namespace Forever\Blog\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
 
class Actions extends Column
{
    const URL_PATH_EDIT = 'blog/index/edit';
    const URL_PATH_DELETE = 'blog/index/delete';

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
 
                if (isset($item['blog_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_EDIT,
                            [
                                    'blog_id' => $item['blog_id']
                                ]
                        ),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_DELETE,
                            [
                                    'blog_id' => $item['blog_id']
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
