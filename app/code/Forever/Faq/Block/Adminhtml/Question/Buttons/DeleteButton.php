<?php

declare(strict_types=1);

namespace Forever\Faq\Block\Adminhtml\Question\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        if (!$this->getId()) {
            return [];
        }

        return [
            'label'      => __('Delete'),
            'class'      => 'delete',
            'on_click'   => 'deleteConfirm(\''
                . __('Are you sure you want to delete this contact ?')
                . '\', \'' . $this->getDeleteUrl() . '\')',
            'sort_order' => 20,
        ];
    }

    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getId()]);
    }
}
