<?php

namespace Forever\Core\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveForeverDesign implements ObserverInterface
{
    /**
     * @var Forever\Core\Model\Cssconfig\Generator
     */
    protected $cssGenerator;

    /**
     * @param \Forever\Core\Model\Cssconfig\Generator $cssgenerator
     */
    public function __construct(
        \Forever\Core\Model\Cssconfig\Generator $cssenerator
    ) {
        $this->cssGenerator = $cssenerator;
    }

    /**
     * @inheritdoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->cssGenerator->generateCss(
            'css',
            $observer->getData("website"),
            $observer->getData("store")
        );
    }
}
