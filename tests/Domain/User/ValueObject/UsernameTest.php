<?php

namespace Tests\Leos\Domain\User\ValueObject;

use Leos\Domain\User\ValueObject\Username;
use PHPUnit\Framework\TestCase;

class UsernameTest extends TestCase
{

    public function testUsernameNotEmpty()
    {
        self::expectExceptionMessage('Username can\'t be empty');

        new Username('');
    }

    public function testUsernameMinLength5()
    {
        self::expectExceptionMessage('Username must contain at least 6 characters');

        new Username('123');
    }

    public function testUsernameMaxLength64()
    {
        self::expectExceptionMessage('Username must contain less than 64 characters');

        new Username('qwertyuioplkjhgfdsazsxcvbnmlkjhgfdswertyuimnbvcdsfghjkloiuytrewsdcvbnmkiuytredfvbbn');
    }

    public function testEmailConvertedToString()
    {
        $usernameString = 'lolaso';

        $username = new Username($usernameString);

        self::assertSame($usernameString, (string) $username);
    }
}
