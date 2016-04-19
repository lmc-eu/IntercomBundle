<?php

namespace IntercomBundle\Entity;

interface BulkInterface
{
    const TIME_FORMAT = 'U';

    /**
     * @return array
     */
    public function toArray();
}
