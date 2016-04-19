<?php

namespace IntercomBundle\Service;

use Intercom\Exception\IntercomException;
use Psr\Log\LoggerInterface;

/**
 * Proxy for all Intercom API services.
 * Catches all IntercomExceptions and log them + in prod environment doesn't show them to user.
 */
class ApiProxy
{
    /** @var ApiServiceInterface */
    private $apiService;

    /** @var LoggerInterface */
    private $logger;

    /** @var bool */
    private $ignoreErrors;

    /**
     * @param ApiServiceInterface $apiService
     * @param LoggerInterface $logger
     * @param bool $ignoreErrors
     */
    public function __construct(ApiServiceInterface $apiService, LoggerInterface $logger, $ignoreErrors)
    {
        $this->apiService = $apiService;
        $this->logger = $logger;
        $this->ignoreErrors = $ignoreErrors;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments = [])
    {
        try {
            return call_user_func_array([$this->apiService, $method], $arguments);
        } catch (IntercomException $e) {
            $this->log($e);

            if (!$this->ignoreErrors) {
                throw $e;
            }

            return false;
        }
    }

    public function log(IntercomException $e)
    {
        $request = $e->getRequest();
        $url = $request->getUrl();
        $params = serialize($request->getParams()->toArray());

        $response = $e->getResponse();
        $code = $response->getStatusCode();
        $body = $response->getBody();

        $this->logger->critical('Intercom API server error.', [
            'url' => $url,
            'parameters' => $params,
            'status_code' => $code,
            'body' => $body,
        ]);
    }
}
