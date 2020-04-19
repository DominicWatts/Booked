<?php

namespace Xigen\Booked\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * AutoShip helper class
 */
class Booked extends AbstractHelper
{
    const BOOKED = 1;
    const NOT_BOOKED = 2;

    /**
     * @var \Psr\Log\LoggerInterfaces
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

     /**
      * @param \Magento\Framework\App\Helper\Context $context
      * @param \Psr\Log\LoggerInterface $logger
      * @param \Magento\Sales\Model\OrderRepository $orderRepository
      * @param SearchCriteriaBuilder $searchCriteriaBuilder
      */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);
    }

    /**
     * Load order by increment Id
     * @param string $incrementId
     * @return \Magento\Sales\Model\Data\Order
     */
    public function getOrderByIncrementId($incrementId = null)
    {
        if (!$incrementId) {
            return false;
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $incrementId, 'eq')
            ->create();
        $order = $this->orderRepository
            ->getList($searchCriteria)
            ->getFirstItem();
        if ($order && $order->getId()) {
            return $order;
        }
        return false;
    }
}
