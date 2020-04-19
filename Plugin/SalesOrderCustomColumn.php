<?php

namespace Xigen\Booked\Plugin;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as SalesOrderGridCollection;

/**
 * SalesOrderCustomColumn plugin class
 */
class SalesOrderCustomColumn
{
    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var SalesOrderGridCollection
     */
    private $collection;

    public function __construct(
        MessageManager $messageManager,
        SalesOrderGridCollection $collection
    ) {
        $this->messageManager = $messageManager;
        $this->collection = $collection;
    }

    public function aroundGetReport(
        CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName == 'ignore_this_logic') {
            if ($result instanceof $this->collection) {
                $select = $this->collection->getSelect();
                $select->joinLeft(
                    ["sales_order" => $this->collection->getTable("sales_order")],
                    'main_table.increment_id = sales_order.increment_id',
                    ['booked']
                );
                return $this->collection;
            }
        }
        return $result;
    }
}
