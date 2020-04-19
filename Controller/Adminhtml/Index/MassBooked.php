<?php

namespace Xigen\Booked\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory;
use Xigen\Booked\Helper\Booked as Helper;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

/**
 * MassBook controller class
 */
class MassBooked extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Psr\Log\LoggerInterfaces
     */
    protected $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory $orderCollectionFactory
     * @param \Xigen\Booked\Helper\Booked $helper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CollectionFactory $orderCollectionFactory,
        Helper $helper,
        ResourceConnection $resource,
        LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->helper = $helper;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute mass action
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest();
        $ids = $request->getPost('selected');
        $booked = $request->getParam('booked');

        if ($ids && $booked) {
            $collection = $this->orderCollectionFactory
                ->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('entity_id', ['in' => $ids]);
            $collectionSize = $collection->getSize();
            $updatedItems = 0;
            foreach ($collection as $item) {

                try {
                    // sales_order table
                    if ($order = $this->helper->getOrderByIncrementId($item->getIncrementId())) {
                        $order->setBooked($booked);
                        $order->save();
                    }
                    // sales_order_grid - yes - direct SQL
                    try {
                        $this->connection->beginTransaction();
                        $this->connection->update(
                            'sales_order_grid',
                            ['booked' => $booked],
                            ['increment_id = ?' => $item->getIncrementId()]
                        );
                        $this->connection->commit();
                    } catch (\Exception $e) {
                        $this->connection->rollBack();
                        $this->logger->critical($e->getMessage());
                    }

                    $updatedItems++;
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        $e->getMessage()
                    );
                }
            }
            if ($updatedItems != 0) {
                if ($collectionSize != $updatedItems) {
                    $this->messageManager->addErrorMessage(
                        __('Failed to update %1 order(s).', $collectionSize - $updatedItems)
                    );
                }
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 order(s) have been updated.', $updatedItems)
                );
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('sales/order/index');
    }
}
