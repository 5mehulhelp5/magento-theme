<?php
namespace Forever\Lazyload\Plugin\Model\Template;

class Filter
{
    const LAZYLOAD_CMS = 'lazyload/general/lazyload_cms';

    /**
     * @var \Forever\Lazyload\Helper\Filter
     */
    protected $filterHelper;

    /**
     * @param \Forever\Lazyload\Helper\Filter $filterHelper
     */
    public function __construct(
        \Forever\Lazyload\Helper\Filter $filterHelper
    ) {
        $this->filterHelper = $filterHelper;
    }

    /**
     * After plugin for CMS template filter
     *
     * @param \Magento\Cms\Model\Template\Filter $filter
     * @param string $result
     * @return string
     */
    public function afterFilter(
        \Magento\Cms\Model\Template\Filter $filter,
        string $result
    ): string {
        if ($this->filterHelper->isEnable()
            && $this->filterHelper->getConfig(self::LAZYLOAD_CMS)
        ) {
            $result = $this->filterHelper->filter($result);
        }
        return $result;
    }
}
