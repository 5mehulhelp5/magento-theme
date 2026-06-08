<?php

namespace Forever\Megamenu\Block\Adminhtml\Menu\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;

class Save extends Generic implements ButtonProviderInterface
{
    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
    
        return [
                'label' => __('Save'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'megamenu_form.megamenu_form',
                                    'actionName' => 'save',
                                    'params' => [true],
                                ],
                            ],
                        ],
                    ],
                ],
                'class_name' => Container::SPLIT_BUTTON,
                'options' => $this->getOptions(),
            ];
    }

    /**
     * Get all options
     *
     * @return array
     */
    protected function getOptions()
    {
    
        $options[] = [
            'id_hard' => 'save_and_new',
            'label' => __('Save & New'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'megamenu_form.megamenu_form',
                                'actionName' => 'save',
                                'params' => [
                                    true, [
                                        'back' => 'add',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
       
        return $options;
    }
}
