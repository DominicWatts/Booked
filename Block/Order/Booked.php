<?php

namespace Xigen\Booked\Block\Order;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Xigen\Booked\Helper\Data\Booked as Helper;

/**
 * Class Comment
 */
class Booked extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Registry|null
     */
    protected $coreRegistry = null;

    /**
     * Comment constructor.
     * @param TemplateContext $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->_isScopePrivate = true;
        $this->_template = 'order/view/info.phtml';
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function hasBooked()
    {
        return strlen($this->getBooked()) > 0;
    }

    /**
     * @return string
     */
    public function getBooked()
    {
        return trim($this->getOrder()->getData(Helper::BOOKED_FIELD_NAME));
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * @return string
     */
    public function getBookedHtml()
    {
        return nl2br($this->escapeHtml($this->getBooked()));
    }
}
