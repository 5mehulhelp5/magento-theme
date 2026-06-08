<?php
namespace Forever\Lazyload\Helper;

use Magento\Store\Model\ScopeInterface;

class Filter extends \Magento\Framework\App\Helper\AbstractHelper
{
    const LAZYLOAD_IMAGES              = 'lazyload/general/lazyload_images';
    const LAZYLOAD_IFRAME              = 'lazyload/general/lazyload_iframes';
    const LAZYLOAD_CLASSES             = 'lazyload/general/lazyload_skipclasses';
    const MODULE_ENABLE                = 'lazyload/general/enable';
    const LAZYLOAD_PLACEHOLDER         = 'lazyload/general/lazyload_placeholder';
    const FOLDER_NAME                  = 'forever/lazyload/';
    const LAZYLOAD_DEFAULT_PLACEHOLDER = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
    const DEFAULT_CLASSES              = 'lazy lazy-loading';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->config       = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Convert content to lazyload html
     */
    public function filter(string $content): string
    {
        if (!$this->isEnable()) {
            return $content;
        }

        if ($this->getConfig(self::LAZYLOAD_IMAGES)) {
            $content = $this->filterImages($content);
        }

        if ($this->getConfig(self::LAZYLOAD_IFRAME)) {
            $content = $this->filterIframes($content);
        }

        return $content;
    }

    /**
     * Filter images with placeholders in the content
     */
    public function filterImages(string $content): string
    {
        $matches = $search = $replace = [];
        preg_match_all('/<img[\s\r\n]+.*?>/is', $content, $matches);

        $placeHolderUrl = $this->getPlaceHolderUrl();
        $lazyClasses    = $this->getLazyClasses();

        if ($placeHolderUrl !== $this->getDefaultPlaceHolderUrl()) {
            $lazyClasses = str_replace('lazy-blur', '', $lazyClasses);
        }

        foreach ($matches[0] as $imgHTML) {
            if (!preg_match("/src=['\"]data:image/is", $imgHTML)
                && strpos($imgHTML, 'data-src') === false
                && !$this->isSkipElement($imgHTML)
            ) {
                $replaceHTML = preg_replace(
                    '/<img(.*?)src=/is',
                    '<img$1src="' . $placeHolderUrl . '" data-src=',
                    $imgHTML
                );

                if (preg_match('/class=["\']/', $replaceHTML)) {
                    $replaceHTML = preg_replace(
                        '/class=(["\'])(.*?)["\']/is',
                        'class=$1' . $lazyClasses . ' $2$1',
                        $replaceHTML
                    );
                } else {
                    $replaceHTML = preg_replace(
                        '/<img/is',
                        '<img class="' . $lazyClasses . '"',
                        $replaceHTML
                    );
                }

                $search[]  = $imgHTML;
                $replace[] = $replaceHTML;
            }
        }

        return str_replace($search, $replace, $content);
    }

    /**
     * Filter iframes with placeholders in the content
     */
    public function filterIframes(string $content): string
    {
        $matches = $search = $replace = [];
        preg_match_all('|<iframe\s+.*?</iframe>|si', $content, $matches);

        $placeHolderUrl = $this->getPlaceHolderUrl();
        $lazyClasses    = $this->getLazyClasses();

        foreach ($matches[0] as $iframeHTML) {
            if (!preg_match("/src=['\"]data:image/is", $iframeHTML)
                && strpos($iframeHTML, 'data-src') === false
                && !$this->isSkipElement($iframeHTML)
            ) {
                $replaceHTML = preg_replace(
                    '/<iframe(.*?)src=/is',
                    '<iframe$1src="' . $placeHolderUrl . '" data-src=',
                    $iframeHTML
                );

                if (preg_match('/class=["\']/', $replaceHTML)) {
                    $replaceHTML = preg_replace(
                        '/class=(["\'])(.*?)["\']/is',
                        'class=$1' . $lazyClasses . ' $2$1',
                        $replaceHTML
                    );
                } else {
                    $replaceHTML = preg_replace(
                        '/<iframe/is',
                        '<iframe class="' . $lazyClasses . '"',
                        $replaceHTML
                    );
                }

                $search[]  = $iframeHTML;
                $replace[] = $replaceHTML;
            }
        }

        return str_replace($search, $replace, $content);
    }

    /**
     * Check if element should be skipped via specific classes
     */
    protected function isSkipElement(string $content): bool
    {
        $skipClasses = $this->getSkipClasses();
        if (empty($skipClasses)) {
            return false;
        }
        $skipClassesQuoted = array_map('preg_quote', $skipClasses);
        $skipClassesORed   = implode('|', $skipClassesQuoted);
        $regex = '/<\s*\w*\s*class\s*=\s*[\'\"](|.*\s)(' . $skipClassesORed . ')(|\s.*)[\'\"].*>/isU';
        return (bool) preg_match($regex, $content);
    }

    /**
     * @return array
     */
    protected function getSkipClasses(): array
    {
        $raw = (string) $this->getConfig(self::LAZYLOAD_CLASSES);
        return array_filter(array_map('trim', explode(',', $raw)));
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function getConfig(string $path)
    {
        return $this->config->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return (bool) $this->getConfig(self::MODULE_ENABLE);
    }

    /**
     * @return string
     */
    public function getPlaceHolderUrl(): string
    {
        $url = (string) $this->getConfig(self::LAZYLOAD_PLACEHOLDER);

        if ($url) {
            $url = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . self::FOLDER_NAME . $url;
        }

        return $url ?: $this->getDefaultPlaceHolderUrl();
    }

    /**
     * @return string
     */
    public function getDefaultPlaceHolderUrl(): string
    {
        return self::LAZYLOAD_DEFAULT_PLACEHOLDER;
    }

    /**
     * @return string
     */
    public function getLazyClasses(): string
    {
        $classes = self::DEFAULT_CLASSES;
        if ($this->getPlaceHolderUrl() === $this->getDefaultPlaceHolderUrl()) {
            $classes .= ' lazy-blur';
        }
        return $classes;
    }
}
