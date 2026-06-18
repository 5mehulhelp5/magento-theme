<?php
namespace Forever\DemoInstaller\Controller\Adminhtml\Demo;

use Forever\DemoInstaller\Model\DemoRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

class Thumbnail extends Action
{
    const ADMIN_RESOURCE = 'Forever_DemoInstaller::demo';

    public function __construct(
        Context $context,
        private DemoRepository $demoRepository,
        private FileDriver $file
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $code = (string)$this->getRequest()->getParam('code');
        $response = $this->getResponse();
        try {
            $demo  = $this->demoRepository->get($code);
            $thumb = $demo->getThumbnail();
            $path  = $thumb ? $demo->getPath() . '/' . ltrim($thumb, '/') : null;
            if ($path && $this->file->isReadable($path)) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mime = $ext === 'png' ? 'image/png' : ($ext === 'webp' ? 'image/webp' : 'image/jpeg');
                $response->setHeader('Content-Type', $mime, true);
                $response->setBody($this->file->fileGetContents($path));
                return $response;
            }
        } catch (\Throwable $e) {
            // fall through to a 1x1 transparent gif
        }
        $response->setHeader('Content-Type', 'image/gif', true);
        $response->setBody(base64_decode('R0lGODlhAQABAAAAACwAAAAAAQABAAA='));
        return $response;
    }
}
