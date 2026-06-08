<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Faq\Block\Adminhtml\Question\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton implements ButtonProviderInterface
{
    /**
     * Get Save Button Data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
    /**
     * Generate Save Button URL for Question Form Save Button
     *
     * @return mixed
     */
    public function getSaveUrl()
    {
        $params = [];

        if (!empty($this->getRequest()->getParam('id'))) {
            $params = ['id' => $this->getRequest()->getParam('id')];
        }
        return $this->getUrl('*/*/action/save', $params);
    }
}
