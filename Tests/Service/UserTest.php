<?php

namespace IntercomBundle\Tests\Service;

use Intercom\IntercomBasicAuthClient;
use IntercomBundle\Service\User;
use IntercomBundle\Tests\AbstractTestCase;
use Mockery as m;

class UserTest extends AbstractTestCase
{
    /** @var User */
    private $user;

    /** @var IntercomBasicAuthClient|m\Mock */
    private $intercomClient;

    public function setUp()
    {
        $this->intercomClient = m::mock(IntercomBasicAuthClient::class);

        $this->user = new User($this->intercomClient);
    }

    /**
     * @param string|null $email
     *
     * @dataProvider emailProvider
     */
    public function testShouldSendNewUserToIntercom($email)
    {
        $expectedArgument = [
            'user_id' => 1214,
            'name' => 'John Snow',
            'signed_up_at' => time(),
            'last_request_at' => time(),
        ];

        if (!empty($email)) {
            $expectedArgument['email'] = $email;
        }

        $this->intercomClient->shouldReceive('createUser')
            ->once()
            ->with($expectedArgument);

        $this->user->create(1214, 'John Snow', $email);
    }

    /**
     * @return array
     */
    public function emailProvider()
    {
        return [
            ['john.snow@knows.nothing'],
            [null],
            [''],
        ];
    }

    public function testShouldSendNewUserWithCustomAttributesToIntercom()
    {
        $this->intercomClient->shouldReceive('createUser')
            ->once()
            ->with([
                'user_id' => 12323,
                'name' => 'John Snow II',
                'signed_up_at' => 434930840,
                'last_request_at' => 434930840,
                'custom_attributes' => [
                    'age' => 16,
                    'sex' => 'female',
                ],
            ]);

        $this->user->create(12323, 'John Snow II', '', 434930840, [
            'age' => 16,
            'sex' => 'female',
        ]);
    }
}
