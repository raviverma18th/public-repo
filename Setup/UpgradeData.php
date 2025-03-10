<?php
/**
 * UpgradeData script to add custom attributes to customer
 * 
 * @category  Eighteentech
 * @package   Eighteentech_VirtualFoot
 */

namespace Eighteentech\VirtualFoot\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Attribute set factory
     *
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * Constructor
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Upgrade data script
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $customerEntity = $eavSetup->getEntityTypeId(Customer::ENTITY);
            $attributeSetId = $eavSetup->getDefaultAttributeSetId($customerEntity);
            $attributeGroupId = $eavSetup->getDefaultAttributeGroupId($customerEntity);

            // Add user_token attribute
            $eavSetup->addAttribute(Customer::ENTITY, 'user_token', [
                'type' => 'varchar',
                'label' => 'User Token',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 999,
                'system' => 0,
            ]);

            $attribute = $eavSetup->getAttribute(Customer::ENTITY, 'user_token');
            $eavSetup->addAttributeToGroup(
                $customerEntity,
                $attributeSetId,
                $attributeGroupId,
                $attribute['attribute_id'],
                '999'
            );

            // Add token_expiry attribute
            $eavSetup->addAttribute(Customer::ENTITY, 'token_expiry', [
                'type' => 'datetime',
                'label' => 'Token Expiry',
                'input' => 'date',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 1000,
                'system' => 0,
            ]);

            $attribute = $eavSetup->getAttribute(Customer::ENTITY, 'token_expiry');
            $eavSetup->addAttributeToGroup(
                $customerEntity,
                $attributeSetId,
                $attributeGroupId,
                $attribute['attribute_id'],
                '1000'
            );

            $customerAttributes = [
                'user_token',
                'token_expiry'
            ];

            foreach ($customerAttributes as $attributeCode) {
                $attribute = $eavSetup->getAttribute(Customer::ENTITY, $attributeCode);
                $eavSetup->updateAttribute(Customer::ENTITY, $attribute['attribute_id'], 'is_used_in_grid', 1);
                $eavSetup->updateAttribute(Customer::ENTITY, $attribute['attribute_id'], 'is_visible_in_grid', 1);
            }
        }

        $setup->endSetup();
    }
}