<?php

declare(strict_types=1);

namespace Forever\Productlabel\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Forever\Productlabel\Model\Config\Product\Productmultisel;
use Forever\Productlabel\Model\Config\Product\Productoption;

class CreateProductAttr implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): self
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute('catalog_product', 'productlabel', [
            'group'                => 'Product Label',
            'label'                => 'Multiselect Attribute',
            'type'                 => 'text',
            'input'                => 'multiselect',
            'source'               => Productmultisel::class,
            'required'             => false,
            'sort_order'           => 30,
            'global'               => Attribute::SCOPE_STORE,
            'used_in_product_listing' => true,
            'backend'              => ArrayBackend::class,
            'visible_on_front'     => false,
        ]);

        $eavSetup->addAttribute('catalog_product', 'productoption', [
            'group'                => 'Product Label',
            'label'                => 'Product Position',
            'type'                 => 'varchar',
            'input'                => 'select',
            'required'             => false,
            'sort_order'           => 40,
            'global'               => Attribute::SCOPE_STORE,
            'used_in_product_listing' => true,
            'source'               => Productoption::class,
            'visible_on_front'     => false,
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
