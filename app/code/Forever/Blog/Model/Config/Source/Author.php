<?php

namespace Forever\Blog\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;

class Author implements OptionSourceInterface
{
    /**
     * @var UserCollectionFactory
     */
    protected $_userFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param UserCollectionFactory $userFactory
     */
    public function __construct(
        UserCollectionFactory $userFactory
    ) {
        $this->_userFactory = $userFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [['label' => __('Please select'), 'value' => null]];
            $collection = $this->_userFactory->create();

            foreach ($collection as $item) {
                $this->options[] = [
                    'label' => $item->getName(),
                    'value' => $item->getName(),
                ];
            }
        }

        return $this->options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
