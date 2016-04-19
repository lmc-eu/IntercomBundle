<?php

namespace IntercomBundle\Service;

use Intercom\IntercomBasicAuthClient;
use Psr\Log\LoggerInterface;

class ApiFactory
{
    /**
     * @param IntercomBasicAuthClient $intercomClient
     * @param LoggerInterface $logger
     * @param bool $ignoreErrors
     * @return Bulk|ApiProxy
     */
    public static function createBulk(IntercomBasicAuthClient $intercomClient, LoggerInterface $logger, $ignoreErrors)
    {
        return new ApiProxy(new Bulk($intercomClient), $logger, $ignoreErrors);
    }

    /**
     * @param IntercomBasicAuthClient $intercomClient
     * @param LoggerInterface $logger
     * @param bool $ignoreErrors
     * @return Event|ApiProxy
     */
    public static function createEvent(IntercomBasicAuthClient $intercomClient, LoggerInterface $logger, $ignoreErrors)
    {
        return new ApiProxy(new Event($intercomClient), $logger, $ignoreErrors);
    }

    /**
     * @param IntercomBasicAuthClient $intercomClient
     * @param LoggerInterface $logger
     * @param bool $ignoreErrors
     * @return User|ApiProxy
     */
    public static function createUser(IntercomBasicAuthClient $intercomClient, LoggerInterface $logger, $ignoreErrors)
    {
        return new ApiProxy(new User($intercomClient), $logger, $ignoreErrors);
    }
}
