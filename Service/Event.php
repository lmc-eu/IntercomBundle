<?php

namespace IntercomBundle\Service;

use Assert\Assertion;
use Intercom\IntercomBasicAuthClient;

class Event implements ApiServiceInterface
{
    /** @var IntercomBasicAuthClient */
    private $intercomClient;

    /**
     * @param IntercomBasicAuthClient $intercomClient
     */
    public function __construct(IntercomBasicAuthClient $intercomClient)
    {
        $this->intercomClient = $intercomClient;
    }

    /**
     * @param string $name
     * @param string $userId
     * @param string|null $createdAt
     * @param array $metadata
     */
    public function create($name, $userId, $createdAt = null, array $metadata = [])
    {
        Assertion::notEmpty($name, 'Event name cannot be empty.');

        $eventData = [
            'event_name' => $name,
            'user_id' => $userId,
            'created_at' => $createdAt ?: time(),
        ];

        if (!empty($metadata)) {
            $eventData['metadata'] = $metadata;
        }

        $this->intercomClient->createEvent($eventData);
    }
}
