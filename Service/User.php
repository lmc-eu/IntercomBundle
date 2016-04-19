<?php

namespace IntercomBundle\Service;

use Assert\Assertion;
use Intercom\IntercomBasicAuthClient;

class User implements ApiServiceInterface
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
     * @param int $id
     * @param string $name
     * @param string $email
     * @param \DateTime|null $createdAt
     * @param array $customAttributes
     */
    public function create(
        $id,
        $name,
        $email,
        \DateTime $createdAt = null,
        array $customAttributes = []
    ) {
        Assertion::greaterThan($id, 0, 'User ID must be greater then 0.');

        $createdAt = $createdAt ?: new \DateTime();
        $createdAtTimestamp = (int) $createdAt->format('U');

        $userData = [
            'user_id' => $id,
            'name' => $name,
            'signed_up_at' => $createdAtTimestamp,
            'last_request_at' => $createdAtTimestamp,
        ];

        if (!empty($email)) {
            $userData['email'] = $email;
        }

        if (!empty($customAttributes)) {
            $userData['custom_attributes'] = $customAttributes;
        }

        $this->intercomClient->createUser($userData);
    }

    /**
     * @param int $id
     * @param array $customAttributes
     */
    public function updateMetadataByUserId($id, array $customAttributes)
    {
        Assertion::greaterThan($id, 0, 'User ID must be greater then 0.');
        Assertion::notEmpty($customAttributes, 'User Metadata must be filled.');

        $userData = [
            'user_id' => $id,
            'custom_attributes' => $customAttributes,
        ];

        $this->intercomClient->createUser($userData);
    }
}
