<?php

namespace IntercomBundle\Entity;

class BulkUser implements BulkInterface
{
    /** @var int */
    private $id;

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $email;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime|null */
    private $lastActivity;

    /** @var array */
    private $customAttributes = [];

    /**
     * @param int $id
     * @param string|null $name
     * @param string|null $email
     * @param \DateTime|null $createdAt
     * @param \DateTime|null $lastActivity
     * @param array $customAttributes
     */
    public function __construct(
        $id,
        $name = null,
        $email = null,
        \DateTime $createdAt = null,
        \DateTime $lastActivity = null,
        array $customAttributes = []
    ) {
        \Assert\that($id)
            ->numeric('User ID have to be number')
            ->greaterThan(0, 'User ID have to be bigger then 0.');

        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->lastActivity = $lastActivity;
        $this->customAttributes = $customAttributes;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $userData = [
            'user_id' => $this->id,
        ];

        if (!empty($this->name)) {
            $userData['name'] = $this->name;
        }

        if (!empty($this->email)) {
            $userData['email'] = $this->email;
        }

        if (!empty($this->createdAt)) {
            $userData['signed_up_at'] = (int) $this->createdAt->format(self::TIME_FORMAT);
        }

        if (!empty($this->lastActivity)) {
            $userData['last_request_at'] = (int) $this->lastActivity->format(self::TIME_FORMAT);
        }

        if (!empty($this->customAttributes)) {
            $userData['custom_attributes'] = $this->customAttributes;
        }

        return [
            'data_type' => 'user',
            'method' => 'post',
            'data' => $userData,
        ];
    }
}
