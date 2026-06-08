<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\StoreLocator\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Directory\Model\CountryFactory;

class CountryName extends Column
{
    /**
     * @var CountryFactory
     */
    private CountryFactory $countryFactory;

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CountryFactory     $countryFactory
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CountryFactory $countryFactory,
        array $components = [],
        array $data = []
    ) {
        $this->countryFactory = $countryFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source - replace country code with country name.
     *
     * @param  array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $countryCode   = $item['country'] ?? '';
                $country       = $this->countryFactory->create()->loadByCode($countryCode);
                $item['country'] = $country->getName() ?? $countryCode;
            }
        }

        return $dataSource;
    }
}
