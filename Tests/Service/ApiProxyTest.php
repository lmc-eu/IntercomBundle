<?php

namespace IntercomBundle\Tests\Service;

use Guzzle\Http\Message\MessageInterface;
use Guzzle\Http\Message\Response;
use GuzzleHttp\Message\Request;
use Intercom\Exception\IntercomException;
use IntercomBundle\Service\ApiProxy;
use IntercomBundle\Service\ApiServiceInterface;
use IntercomBundle\Tests\AbstractTestCase;
use Mockery as m;
use Psr\Log\LoggerInterface;

class ApiProxyTest extends AbstractTestCase
{
    /** @var ApiServiceInterface|m\Mock */
    private $apiService;

    /** @var LoggerInterface|m\Mock */
    private $logger;

    public function setUp()
    {
        $this->apiService = m::mock(ApiServiceInterface::class);
        $this->logger = m::mock(LoggerInterface::class);
    }

    /**
     * @param bool $ignoreErrors
     * @param bool $isExpectedException
     *
     * @dataProvider environmentProvider
     */
    public function testShouldCreateProxyAndThrowExceptionByEnvironment($ignoreErrors, $isExpectedException)
    {
        if ($isExpectedException) {
            $this->setExpectedException(IntercomException::class);
        }

        $this->apiService->shouldReceive('dummyFunction')
            ->with('dummyArg')
            ->once()
            ->andThrow($this->getIntercomExceptionMock());

        $this->logger->shouldReceive('critical')
            ->once()
            ->with(
                'Intercom API server error.',
                [
                    'url' => 'request url',
                    'parameters' => 'a:2:{s:7:"param 1";s:7:"value 1";s:7:"param 2";s:7:"value 2";}',
                    'status_code' => 798,
                    'body' => 'dummy response body',
                ]
            );

        $proxy = new ApiProxy($this->apiService, $this->logger, $ignoreErrors);

        $this->assertFalse($proxy->dummyFunction('dummyArg'));
    }

    /**
     * @return array
     */
    public function environmentProvider()
    {
        return [
            // ignore errors?, expected exception?
            'PROD' => [true, false],
            'DEV' => [false, true],
        ];
    }

    /**
     * @return IntercomException|m\Mock
     */
    private function getIntercomExceptionMock()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('getUrl')
            ->once()
            ->andReturn('request url');
        $request->shouldReceive('getParams')
            ->once()
            ->andReturnUsing(function () {
                $message = m::mock(MessageInterface::class);
                $message->shouldReceive('toArray')
                    ->once()
                    ->andReturn([
                        'param 1' => 'value 1',
                        'param 2' => 'value 2',
                    ]);

                return $message;
            });

        $response = m::mock(Response::class);
        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(798);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('dummy response body');

        $exception = m::mock(IntercomException::class);
        $exception->shouldReceive('getRequest')
            ->once()
            ->andReturn($request);
        $exception->shouldReceive('getResponse')
            ->once()
            ->andReturn($response);

        return $exception;
    }
}
