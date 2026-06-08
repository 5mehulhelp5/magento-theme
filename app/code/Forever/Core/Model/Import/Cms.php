<?php

namespace Forever\Core\Model\Import;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as BlockCollectionFactory;
use Magento\Cms\Model\BlockFactory as BlockFactory;
use Magento\Cms\Model\ResourceModel\Block as BlockResourceBlock;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Cms\Model\PageFactory as PageFactory;
use Magento\Cms\Model\ResourceModel\Page as PageResourceBlock;

class Cms extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \app/code/Forever/Core/etc/import
     */
    private $_importPath;
    
    /**
     * @var \Magento\Framework\Xml\Parser
     */
    protected $_parser;
    
    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory
     */
    protected $_blockCollectionFactory;

    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterface
     */
    protected $_blockRepository;
    
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;
    
    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $_pageCollectionFactory;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    protected $_pageRepository;
    
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileSystem;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param BlockCollectionFactory $blockCollectionFactory
     * @param \Magento\Cms\Api\BlockRepositoryInterface $blockRepository
     * @param BlockFactory $blockFactory
     * @param PageCollectionFactory $pageCollectionFactory
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepository
     * @param PageFactory $pageFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        BlockCollectionFactory $blockCollectionFactory,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        BlockFactory $blockFactory,
        PageCollectionFactory $pageCollectionFactory,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        PageFactory $pageFactory,
        \Magento\Framework\Filesystem\Driver\File $fileSystem
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_blockCollectionFactory = $blockCollectionFactory;
        $this->_blockFactory = $blockFactory;
        $this->_blockRepository = $blockRepository;
        $this->_pageCollectionFactory = $pageCollectionFactory;
        $this->_pageFactory = $pageFactory;
        $this->fileSystem = $fileSystem;
        $this->_pageRepository = $pageRepository;
        $this->_importPath = BP . '/app/code/Forever/Core/etc/import/';
        $this->_parser = new \Magento\Framework\Xml\Parser();
    }

    /**
     * This Funcation Save Block And Pages Content and Display Message.
     *
     * @return Mixed | String | Int
     */
    public function importCms($type)
    {
        $gatewayResponse = new DataObject([
            'is_valid' => false,
            'import_path' => '',
            'request_success' => false,
            'request_message' => __('Error during Import CMS Sample Data.'),
        ]);
        try {
            $xmlPath = $this->_importPath . $type . '.xml';
            
            $overwrite = false;

            if (!$this->fileSystem->isReadable($xmlPath)) {
                $this->messageManager->addErrorMessage(__("Can't get the data file for import cms blocks/pages: " . $xmlPath));
            }
            $data = $this->_parser->load($xmlPath)->xmlToArray();
            $cms_collection = null;
            $conflictingOldItems = [];
            
            $i = 0;
            $overwrite = true;
            $items = $data['root'][$type]['cms_item'];
            foreach ($items as $_item) {
                $exist = false;
                if ($_item['identifier']) {
                    if ($type == "blocks") {
                        $cms_collection = $this->_blockCollectionFactory->create()
                        ->addFieldToFilter('identifier', $_item['identifier']);
                        if (count($cms_collection) > 0) {
                            $exist = true;
                        }
                    } else {
                        $cms_collection = $this->_pageCollectionFactory->create()
                        ->addFieldToFilter('identifier', $_item['identifier']);
                        if (count($cms_collection) > 0) {
                            $exist = true;
                        }
                    }
                    if ($overwrite) {
                        if ($exist) {
                            $conflictingOldItems[] = $_item['identifier'];
                            $value = $cms_collection->getFirstItem();
                            if ($type == "blocks" && $value->getId()) {
                                $value->setContent($_item['content'])->save();
                            } else {
                                     $value->setContent($_item['content'])->save();
                            }
                        }
                    } else {
                        if ($exist) {
                            $conflictingOldItems[] = $_item['identifier'];
                            continue;
                        }
                    }
                    $_item['stores'] = [0];
                    if ($type == "blocks" && !$exist) {
                        $this->_blockFactory->create()->setData($_item)->save();
                    }
                    if ($type == "pages" && !$exist) {
                        $this->_pageFactory->create()->setData($_item)->save();
                    }
                    $i++;
                }
            }
            $message = "";
            if ($i) {
                $message = $i . " item(s) was(were) imported.";
            } else {
                $message = "No items were imported.";
            }
            
            $gatewayResponse->setIsValid(true);
            $gatewayResponse->setRequestSuccess(true);
            
            if ($gatewayResponse->getIsValid()) {
                if ($overwrite) {
                    if ($conflictingOldItems) {
                        $message .= "Items (".count($conflictingOldItems).")
                         with the following identifiers were overwritten:<br/>" . implode(', ', $conflictingOldItems);
                    }
                } else {
                    if ($conflictingOldItems) {
                        $message .= "<br/>Unable to import items (".count($conflictingOldItems).") 
                        with the following identifiers (they already exist in the database):<br/>"
                        .implode(', ', $conflictingOldItems);
                    }
                }
            }
            $gatewayResponse->setRequestMessage(__($message));
        } catch (\Exception $exception) {
            $gatewayResponse->setIsValid(false);
            $gatewayResponse->setRequestMessage($exception->getMessage());
        }

        return $gatewayResponse;
    }
}
