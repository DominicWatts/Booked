<?php

namespace Xigen\Booked\Helper\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use Xigen\Booked\Api\Data\BookedInterface;

/**
 * Class OrderComment
 */
class Booked extends AbstractSimpleObject implements BookedInterface
{
    const BOOKED_FIELD_NAME = 'booked';

    /**
     * @return mixed|string|null
     */
    public function getBooked()
    {
        return $this->_get(static::BOOKED_FIELD_NAME);
    }

    /**
     * @param string $booked
     * @return Booked|null
     */
    public function setBooked($booked)
    {
        return $this->setData(static::BOOKED_FIELD_NAME, $booked);
    }
}
