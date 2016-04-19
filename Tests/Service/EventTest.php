<?php

namespace IntercomBundle\Tests\Service;

use Assert\AssertionFailedException;
use Intercom\IntercomBasicAuthClient;
use IntercomBundle\Service\Event;
use IntercomBundle\Tests\AbstractTestCase;
use Mockery as m;

class EventTest extends AbstractTestCase
{
    /** @var Event */
    private $event;

    /** @var IntercomBasicAuthClient|m\Mock */
    private $intercomClient;

    public function setUp()
    {
        $this->intercomClient = m::mock(IntercomBasicAuthClient::class);

        $this->event = new Event($this->intercomClient);
    }

    public function testShouldSendNewEventToIntercom()
    {
        $this->intercomClient->shouldReceive('createEvent')
            ->once()
            ->with([
                'event_name' => 'dummy event',
                'user_id' => 1214,
                'created_at' => time(),
            ]);

        $this->event->create('dummy event', 1214);
    }

    public function testShouldSendNewEventToIntercomWithTimestampAndMetadata()
    {
        $this->intercomClient->shouldReceive('createEvent')
            ->once()
            ->with([
                'event_name' => 'dummy event 2',
                'user_id' => 1111,
                'created_at' => 43489038094,
                'metadata' => [
                    'something' => 'dummy',
                    'another attribute' => 'dummy 2',
                ],
            ]);

        $this->event->create('dummy event 2', 1111, 43489038094, [
            'something' => 'dummy',
            'another attribute' => 'dummy 2',
        ]);
    }

    public function testShouldNotSendEventWithoutNameToIntercom()
    {
        $this->setExpectedException(AssertionFailedException::class);

        $this->intercomClient->shouldNotReceive('createEvent');

        $this->event->create('', 1214);
    }
}
