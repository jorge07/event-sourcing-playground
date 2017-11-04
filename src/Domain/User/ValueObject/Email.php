<?php

namespace Leos\Domain\User\ValueObject;

use Assert\Assertion;

final class Email
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        self::validate($email);

        $this->email = $email;
    }

    private static function validate(string $email): void
    {
        Assertion::email($email, 'Email not valid');
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
