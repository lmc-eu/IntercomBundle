<?php

namespace IntercomBundle\Tests\Service;

use Assert\AssertionFailedException;
use Guzzle\Service\Resource\Model;
use Intercom\IntercomBasicAuthClient;
use IntercomBundle\Entity\BulkUser;
use IntercomBundle\Service\Bulk;
use IntercomBundle\Tests\AbstractTestCase;
use Mockery as m;

class BulkTest extends AbstractTestCase
{
    /** @var Bulk */
    private $bulk;

    /** @var IntercomBasicAuthClient|m\Mock */
    private $intercomClient;

    public function setUp()
    {
        $this->intercomClient = m::mock(IntercomBasicAuthClient::class);

        $this->bulk = new Bulk($this->intercomClient);
    }

    public function testShouldNotSendMoreThen100ItemsInBulk()
    {
        $this->setExpectedException(AssertionFailedException::class);

        $tooBigArray = range(0, 500);

        $this->bulk->createUsers($tooBigArray);
    }

    public function testShouldSendBulkUsers()
    {
        $users = [
            new BulkUser(
                123,
                'John Snow',
                'john.snow@knows.nothing',
                $this->dateTime(30480850984),
                null,
                [
                    'login_type' => 'email',
                ]
            ),
            new BulkUser(
                7808,
                'Mr. Anderson',
                '',
                $this->dateTime(49034893084),
                null
            ),
            new BulkUser(
                666,
                'Darth Vader',
                null,
                $this->dateTime(8970384903),
                null,
                [
                    'login_type' => 'linkedin',
                ]
            ),
        ];

        /** @var Model|m\Mock $result */
        $result = m::mock(Model::class);
        $result->shouldReceive('get')
            ->with('id')
            ->once()
            ->andReturn('job_id_5048');

        $this->intercomClient->shouldReceive('bulkUsers')
            ->once()
            ->with([
                'items' => [
                    [
                        'data_type' => 'user',
                        'method' => 'post',
                        'data' => [
                            'user_id' => 123,
                            'name' => 'John Snow',
                            'signed_up_at' => 30480850984,
                            'email' => 'john.snow@knows.nothing',
                            'custom_attributes' => [
                                'login_type' => 'email',
                            ],
                        ],
                    ],
                    [
                        'data_type' => 'user',
                        'method' => 'post',
                        'data' => [
                            'user_id' => 7808,
                            'name' => 'Mr. Anderson',
                            'signed_up_at' => 49034893084,
                        ],
                    ],
                    [
                        'data_type' => 'user',
                        'method' => 'post',
                        'data' => [
                            'user_id' => 666,
                            'name' => 'Darth Vader',
                            'signed_up_at' => 8970384903,
                            'custom_attributes' => [
                                'login_type' => 'linkedin',
                            ],
                        ],
                    ],
                ],
            ])
            ->andReturn($result);

        $this->assertEquals('job_id_5048', $this->bulk->createUsers($users));
    }
}
