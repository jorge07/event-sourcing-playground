<?php

namespace Leos\Domain\User\ValueObject;

use Assert\Assertion;

class Username
{
    /**
     * @var string
     */
    private $username;

    public function __construct(string $username)
    {
        self::validate($username);
        $this->username = $username;
    }

    private static function validate(string $username)
    {
        Assertion::notEmpty($username, 'Username can\'t be empty');
        Assertion::minLength($username, 5, 'Username must contain at least 6 characters');
        Assertion::maxLength($username, 64, 'Username must contain less than 64 characters');
    }

    public function __toString(): string
    {
        return $this->username;
    }
}
