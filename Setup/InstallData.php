<?php

namespace Xigen\Booked\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Install script to add booked attribute to sales table
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * Setup constructor
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $salesSetup = $this->salesSetupFactory
            ->create(['setup' => $setup]);

        $salesSetup->addAttribute(
            Order::ENTITY,
            'booked',
            [
                'type' => Table::TYPE_SMALLINT,
                'length' => 5,
                'default' => 2,
                'unsigned' => true,
                'nullable' => false,
                'visible' => true,
                'required' => false,
                'grid' => true
            ]
        );
    }
}
