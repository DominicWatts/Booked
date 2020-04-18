<?php

namespace Xigen\Booked\Ui\Component\Listing\Column;

use Xigen\Booked\Helper\Booked as Helper;

/**
 * Shipment ui listing column class
 */
class Booked implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Helper::BOOKED,
                'label' => __('Yes')
            ],
            [
                'value' => Helper::NOT_BOOKED,
                'label' => __('No')
            ]
        ];
    }
}
