<?php

namespace Leos\Domain\User\Repository;

use Leos\Domain\User\Aggregate\User;
use Leos\Domain\User\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function get(UserId $uuid): User;
}
