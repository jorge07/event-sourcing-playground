<?php

namespace Tests\Leos\Domain\User\ValueObject;

use Leos\Domain\User\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{

    public function testInvalidEmail()
    {
        self::expectExceptionMessage('Email not valid');

        new Email('');
    }

    public function testValidEmail()
    {
        $email = new Email('j@j.com');

        self::assertSame('j@j.com', (string) $email);
    }
}
