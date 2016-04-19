<?php

namespace IntercomBundle\Entity;

use Assert\Assertion;

class BulkEvent implements BulkInterface
{
    /** @var int */
    private $id;

    /** @var \DateTime */
    private $createdAt;

    /** @var int */
    private $userId;

    /**
     * @param int $eventName
     * @param \DateTime $createdAt
     * @param int $userId
     */
    public function __construct($eventName, \DateTime $createdAt, $userId)
    {
        Assertion::notEmpty($eventName, 'Event name can not be empty.');
        \Assert\that($userId)
            ->numeric('User ID has to be number')
            ->greaterThan(0, 'User ID have to be bigger then 0.');

        $this->id = $eventName;
        $this->createdAt = $createdAt;
        $this->userId = $userId;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'data_type' => 'event',
            'method' => 'post',
            'data' => [
                'event_name' => $this->id,
                'created_at' => $this->createdAt->format(self::TIME_FORMAT),
                'user_id' => $this->userId,
            ],
        ];
    }
}
