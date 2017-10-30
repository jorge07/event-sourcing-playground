<?php

namespace Leos\Application\Command\User;

use Leos\Domain\User\ValueObject\Email;
use Leos\Domain\User\ValueObject\UserId;
use Leos\Domain\User\ValueObject\Username;

class SignUpCommand implements \JsonSerializable
{
    /**
     * @var UserId
     */
    public $uuid;

    /**
     * @var Username
     */
    public $username;

    /**
     * @var Email
     */
    public $email;

    public function __construct(string $uuid, string $username, string $email)
    {
        $this->uuid = UserId::fromString($uuid);
        $this->username = new Username($username);
        $this->email = new Email($email);
    }

    public function jsonSerialize()
    {
        return [
            'uuid' => $this->uuid->toString(),
            'username' => $this->username->__toString(),
            'email' => $this->email->__toString()
        ];
    }
}
