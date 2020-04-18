<?php

namespace Xigen\Booked\Api\Data;

/**
 * Interface BookedInterface
 */
interface BookedInterface
{
    /**
     * @return string|null
     */
    public function getBooked();

    /**
     * @param string $booked
     * @return null
     */
    public function setBooked($booked);
}
