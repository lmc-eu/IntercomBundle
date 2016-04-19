<?php

namespace IntercomBundle\Entity;

class BulkUser implements BulkInterface
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $email;

    /** @var \DateTime */
    private $createdAt;

    /** @var array */
    private $customAttributes = [];

    /**
     * @param int $id
     * @param string $name
     * @param string $email
     * @param \DateTime $createdAt
     * @param array $customAttributes
     */
    public function __construct($id, $name, $email, \DateTime $createdAt, array $customAttributes = [])
    {
        \Assert\that($id)
            ->numeric('User ID have to be number')
            ->greaterThan(0, 'User ID have to be bigger then 0.');

        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->customAttributes = $customAttributes;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $user = [
            'data_type' => 'user',
            'method' => 'post',
            'data' => [
                'user_id' => $this->id,
                'name' => $this->name,
                'signed_up_at' => $this->createdAt->format(self::TIME_FORMAT),
            ],
        ];

        if (!empty($this->email)) {
            $user['data']['email'] = $this->email;
        }

        if (!empty($this->customAttributes)) {
            $user['data']['custom_attributes'] = $this->customAttributes;
        }

        return $user;
    }
}
