<?php

namespace Xigen\Booked\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

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
     * MassBooked constructor
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CollectionFactory $orderCollectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
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
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', ['in' => $ids]);
            $collectionSize = $collection->getSize();
            $updatedItems = 0;
            foreach ($collection as $item) {
                try {
                    $item->setBooked($booked);
                    $item->save();
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
