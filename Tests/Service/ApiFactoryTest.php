<?php

namespace IntercomBundle\Tests\Service;

use Intercom\IntercomBasicAuthClient;
use IntercomBundle\Service\ApiFactory;
use IntercomBundle\Service\ApiProxy;
use IntercomBundle\Service\Bulk;
use IntercomBundle\Service\Event;
use IntercomBundle\Service\User;
use IntercomBundle\Tests\AbstractTestCase;
use Mockery as m;
use Psr\Log\LoggerInterface;

class ApiFactoryTest extends AbstractTestCase
{
    /** @var IntercomBasicAuthClient|m\Mock */
    private $intercomClient;

    /** @var LoggerInterface|m\Mock */
    private $logger;

    public function setUp()
    {
        $this->intercomClient = m::mock(IntercomBasicAuthClient::class);
        $this->logger = m::mock(LoggerInterface::class);
    }

    public function testShouldCreateBulkService()
    {
        $bulkService = ApiFactory::createBulk($this->intercomClient, $this->logger, true);

        $this->assertInstanceOf(ApiProxy::class, $bulkService);
        $this->assertProxyInstanceOf(Bulk::class, $bulkService);
    }

    /**
     * @param string $expectedClass
     * @param ApiProxy $service
     */
    private function assertProxyInstanceOf($expectedClass, ApiProxy $service)
    {
        $reflection = new \ReflectionClass($service);
        $apiService = $reflection->getProperty('apiService');
        $apiService->setAccessible(true);

        $this->assertInstanceOf($expectedClass, $apiService->getValue($service));
    }

    public function testShouldCreateEventService()
    {
        $eventService = ApiFactory::createEvent($this->intercomClient, $this->logger, true);

        $this->assertInstanceOf(ApiProxy::class, $eventService);
        $this->assertProxyInstanceOf(Event::class, $eventService);
    }

    public function testShouldCreateUserService()
    {
        $userService = ApiFactory::createUser($this->intercomClient, $this->logger, true);

        $this->assertInstanceOf(ApiProxy::class, $userService);
        $this->assertProxyInstanceOf(User::class, $userService);
    }
}
