<?php

namespace IntercomBundle\Service;

use Assert\Assertion;
use Intercom\IntercomBasicAuthClient;
use IntercomBundle\Entity\BulkEvent;
use IntercomBundle\Entity\BulkInterface;
use IntercomBundle\Entity\BulkUser;

class Bulk implements ApiServiceInterface
{
    const ITEMS_PER_REQUEST_LIMIT = 100; // 100 is maximum value set by Intercom

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
     * @param BulkUser[] $users
     * @return string
     */
    public function createUsers(array $users)
    {
        $this->assertBulkSize($users);

        $result = $this->intercomClient->bulkUsers([
            'items' => array_map([$this, 'getItemArray'], $users),
        ]);

        return $result->get('id');
    }

    /**
     * @param array $bulk
     */
    private function assertBulkSize(array $bulk)
    {
        Assertion::lessOrEqualThan(
            count($bulk),
            self::ITEMS_PER_REQUEST_LIMIT,
            sprintf('Maximum number of items in Intercom bulk is %d.', self::ITEMS_PER_REQUEST_LIMIT)
        );
    }

    /**
     * @param BulkInterface $item
     * @return array
     */
    private function getItemArray(BulkInterface $item)
    {
        return $item->toArray();
    }

    /**
     * @param BulkEvent[] $events
     * @return string
     */
    public function createEvents(array $events)
    {
        $this->assertBulkSize($events);

        $result = $this->intercomClient->bulkEvents([
            'items' => array_map([$this, 'getItemArray'], $events),
        ]);

        return $result->get('id');
    }

    /**
     * @param int $jobId
     * @return array
     */
    public function getJobErrors($jobId)
    {
        $errors = [];

        $result = $this->intercomClient->getJobErrors([
            'id' => $jobId,
        ]);

        $items = $result->getAll()['items'];

        foreach ($items as $item) {
            foreach ($item['error'] as $error) {
                $errors[] = $error['message'];
            }
        }

        return $errors;
    }
}
