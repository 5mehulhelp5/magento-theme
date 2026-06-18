<?php
namespace Forever\DemoInstaller\Block\Adminhtml\Demo;

use Forever\DemoInstaller\Model\DemoRepository;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class Listing extends Template
{
    public function __construct(Context $context, private DemoRepository $demoRepository, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /** @return \Forever\DemoInstaller\Model\Demo[] */
    public function getDemos(): array
    {
        return $this->demoRepository->getList();
    }

    public function getImportUrl(): string
    {
        return $this->getUrl('forever_demo/demo/import');
    }

    public function getThumbnailUrl(string $code): string
    {
        return $this->getUrl('forever_demo/demo/thumbnail', ['code' => $code]);
    }
}
